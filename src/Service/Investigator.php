<?php

//src/Service/Crawler.php

namespace App\Service;

use App\Entity\RV;
use App\Entity\Summary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of Crawler
 *
 * @author George
 */
class Investigator
{

    private $em;
    private $years = ['2014', '2015', '2016', '2017', '2018', '2019', '2020'];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function rvt($dir, $file)
    {
        $fullName = $dir . $file->getFilename();
        $html = \file_get_contents($fullName);
        $crawler = new Crawler($html);

        $titleText = $crawler->filter('title')->text();
        $rv['class'] = 'C';
        if (strpos($titleText, ' B+ ') > 1) {
            $rv['class'] = 'B+';
        }

        $filter = 'ol.ais-Hits-list';
        $lis = $crawler->filter($filter);
        $filter2 = 'li[itemtype="http://schema.org/Product"]';
        // find n nodes containing rvt searches
        $nodes = $lis->eq(2)->filter($filter2);
        $n = count($nodes);

        for ($i = 0; $i < $n; $i++) {
            $x = $nodes->eq($i)->children('div')->filter('div.result-content');
            $price = $x->filter('span.price > span')->text();
            $a = $nodes->eq($i)->children('div')->filter('a.result-link');
            $rv['url'] = $a->attr('href');
            $ymm = $x->filter('h5')->text();
            $rv['ymm'] = $ymm;
            $rv['year'] = substr($ymm, 0, 4);
            $priceString = preg_replace("/[^0-9]/", '', $price);
            $place = $x->filter('span.location')->text();
            $rv['location'] = $this->conformLocation($place);

            if (0 < intval($priceString) && in_array($rv['year'], $this->years)) {
                $rv['price'] = intval($priceString);
                $this->addToDB($rv, $file);
                $entry[$i] = $rv;
            }
        }

        if (isset($entry)) {
            return ['rvs' => $entry, 'class' => $rv['class']];
        }

        return [];
    }

    public function rvtrader($dir, $file)
    {
        $fullName = $dir . $file->getFilename();
        $html = file_get_contents($fullName);

        $fieldMap = [
            'data-ymm' => 'ymm',
            'data-url' => 'url',
            'data-ad_make' => 'make',
            'data-ad_model' => 'model',
            'data-ad_price' => 'price',
            'data-ad_location' => 'location',
            'data-ad_year' => 'year',
        ];

        $crawler = new Crawler($html);
        $filter = "div.margin-bottom30.bgWhite.boxShadow:nth-child(1)";
        $nodes = $crawler->filter($filter);
        $n = count($nodes);
        for ($i = 0; $i < $n; $i++) {
            $html = $nodes->eq($i)->html();
            foreach ($fieldMap as $key => $value) {
                $attr = $key;
                $len = strlen($attr . '="');
                $pos = strpos($html, $attr);
                $start = $pos + $len;
                $end = stripos($html, '"', $start);

                $item = substr($html, $start, $end - $start);
                if ('location' === $value) {
                    $item = $this->conformLocation($item);
                }
                if ('price' === $value) {
                    $item = (int) $item;
                }
                $rv[$value] = $item;
                $rv['class'] = 'C';
            }

            if (in_array($rv['year'], $this->years)) {
                $this->addToDB($rv, $file);
                $entry[$i] = $rv;
            }
        }
        if (isset($entry)) {
            return ['rvs' => $entry, 'class' => $rv['class']];
        }
    }

    public function rvusa($dir, $file)
    {
        $fullName = $dir . $file->getFilename();
        $html = \file_get_contents($fullName);
        $crawler = new Crawler($html);

        $titleText = $crawler->filter('title')->text();
        $rv['class'] = 'C';
        if (strpos($titleText, ' B+ ') > 1) {
            $rv['class'] = 'B+';
        }

        $filter = '.row.listing-top';
        $nodes = $crawler->filter($filter);
        $n = count($nodes);

        for ($i = 0; $i < $n; $i++) {
            $text = $nodes->eq($i)->filter('a.inv-unit h2')->text();
            $year = substr($text, 5, 4);
            $ymm = substr($text, 5, 99);
            $priceText = $nodes->eq($i)->filter('h2.inv-price')->text();
            $place = $nodes->eq($i)->filter('.inv-unit-city-state')->text();
            $digits = preg_replace("/[^0-9]/", '', $priceText);
            $price = substr($digits, 0, strlen($digits) - 2);
            $rv['year'] = $year;
            $rv['ymm'] = $ymm;
            // strip zip code from location so that locations may be compared
            $rv['location'] = $this->conformLocation($place);
            if (0 < intval($price) && in_array($rv['year'], $this->years)) {
                $rv['price'] = intval($price);
                $this->addToDB($rv, $file);
                $entry[$i] = $rv;
            }
        }
        if (isset($entry)) {
            return ['rvs' => $entry, 'class' => $rv['class']];
        }

        return [];
    }

    private function addToDB($newRv, $file)
    {
        $rv = new RV();
        $rv->setLocation($newRv['location'] ?? null);
        $rv->setPrice($newRv['price'] ?? null);
        $rv->setYear($newRv['year'] ?? null);
        $rv->setYmm($newRv['ymm'] ?? null);
        $rv->setClass($newRv['class'] ?? null);
        $rv->setFile($file);
        $this->em->persist($rv);
    }

    public function manageSummary($file, $class)
    {
        $filename = $file->getFilename();
        $fileDate = new \DateTime(substr($filename, 0, 8));
        $summary = $this->em->getRepository(Summary::class)->findOneBy(['added' => $fileDate, 'class' => $class]);
        if (null === $summary) {
            $summary = new Summary();
            $summary->setAdded($fileDate);
            $summary->setClass($class);
        }
        $file->setDates($summary);
        $this->em->persist($file);
        $this->em->flush();

        // returns arrays with keys added, class, year, (sum of)price, (count of rvs)n
        $dataSet = $this->em->getRepository(RV::class)->rvsFromFile($file);

        foreach ($this->years as $year) {
            $getterAvgPrice = 'getYr' . $year;
            $getterN = 'getN' . $year;
            $n = $summary->$getterN();
            $price = $summary->$getterAvgPrice() * $n;
            $updater[$year] = ['price' => $price, 'n' => $n];
        }

        foreach ($dataSet as $row) {
            $year = $row['year'];
            $updater[$year]['price'] += $row['price'];
            $updater[$year]['n'] += $row['n'];
        }

        foreach ($this->years as $year) {
            $setterAvgPrice = 'setYr' . $year;
            $setterN = 'setN' . $year;
            $n = $updater[$year]['n'];
            if (0 < $n) {
                $avg = round($updater[$year]['price'] / $n, 0);
                $summary->$setterN($n);
                $summary->$setterAvgPrice($avg);
            }
        }
        $this->em->persist($summary);

        $this->em->flush();

        return $summary;
    }

    // private seller found
    // rvusa: strtoupper($rv['location']) == 'PRIVATE SELLER'
    // rvtrader: $filter = 'div.searchCardCta span.seller-name';
    //          'Private Seller' === $nodes->eq($i)->filter($filter)->text();

    public function testFile()
    {
        $dir = 'G:\\workspace\\scraper\\var\\pages\\';
        $file = '20210217_rvtrader.com_16-47-29' . '.html';
        $html = file_get_contents($dir . $file);
        $fieldMap = [
            'data-ymm' => 'ymm',
            'data-url' => 'url',
            'data-ad_make' => 'make',
            'data-ad_model' => 'model',
            'data-ad_price' => 'price',
            'data-ad_location' => 'location',
            'data-ad_year' => 'year',
        ];

        $crawler = new Crawler($html);
        $filter = "div.margin-bottom30.bgWhite.boxShadow:nth-child(1)";
        $nodes = $crawler->filter($filter);
        $n = count($nodes);
        for ($i = 0; $i < $n; $i++) {
            $html = $nodes->eq($i)->html();
            foreach ($fieldMap as $key => $value) {
                $attr = $key;
                $len = strlen($attr . '="');
                $pos = strpos($html, $attr);
                $start = $pos + $len;
                $end = stripos($html, '"', $start);

                $item = substr($html, $start, $end - $start);
                if ('location' === $value) {
                    $item = $this->conformLocation($item);
                }
                if ('price' === $value) {
                    $item = (int) $item;
                }
                $rv[$value] = $item;
                $rv['class'] = 'C';
            }

//            if (in_array($rv['year'], $this->years)) {
//                $this->addToDB($rv, $file);
//                $entry[$i] = $rv;
//            }
        }
    }

    private function conformLocation($location)
    {
        // removes zip code
        if (is_numeric(substr($location, -5))) {
            return substr($location, 0, strlen($location) - 6);
        }

        // removes mileage from 89523
        if (strpos($location, '(') > 0) {
            return substr($location, 0, strpos($location, '(') - 1);
        }

        return $location;
    }

}
