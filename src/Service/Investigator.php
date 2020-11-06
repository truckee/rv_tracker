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
            if ('SOLD' !== $price) {
                $a = $nodes->eq($i)->children('div')->filter('a.result-link');
                $rv['url'] = $a->attr('href');
                $ymm = $x->filter('h5')->text();
                $rv['ymm'] = $ymm;
                $rv['year'] = substr($ymm, 0, 4);
                $priceString = preg_replace("/[^0-9]/", '', $price);
                $rv['location'] = $x->filter('span.location')->text();
                if (0 < intval($priceString)) {
                    $rv['price'] = intval($priceString);
                    $this->addToDB($rv, $file);
                    $entry[$i] = $rv;
                }
            }
        }
        if (isset($entry)) {
            $this->em->flush();

            return $entry;
        }

        return [];
    }

    public function rvtrader($dir, $file)
    {
//        $fullName = $dir . $file->getFilename();
//        $html = file_get_contents($fullName);
//
//        $fieldMap = [
//            'data-ymm' => 'ymm',
//            'data-url' => 'url',
//            'data-ad_make' => 'make',
//            'data-ad_model' => 'model',
//            'data-ad_price' => 'price',
//            'data-ad_location' => 'location',
//            'data-ad_year' => 'year',
//        ];
//
//        $crawler = new Crawler($html);
//        $filter = "div.margin-bottom30.bgWhite.boxShadow:nth-child(1)";
//        $divs = $crawler->filter($filter);
//        $n = count($divs);
//        for ($i = 0; $i < $n; $i++) {
//            $html = $divs->eq($i)->html();
//            foreach ($fieldMap as $key => $value) {
//                $attr = $key;
//                $len = strlen($attr . '="');
//                $pos = strpos($html, $attr);
//                $start = $pos + $len;
//                $end = stripos($html, '"', $start);
//
//                $item = substr($html, $start, $end - $start);
//                $rv[$value] = ($value === 'url') ? 'https://www.rvtrader.com' . $item : $item;
//                $rv['class'] = 'C';
//            }
//            $this->addToDB($rv, $file);
//            $entry[$i] = $rv;
//        }
//        $this->em->flush();
//
//        return $entry;
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
            $location = $nodes->eq($i)->filter('.inv-unit-city-state')->text();
            $digits = preg_replace("/[^0-9]/", '', $priceText);
            $price = substr($digits, 0, strlen($digits) - 2);
            $rv['year'] = $year;
            $rv['ymm'] = $ymm;
            $rv['location'] = $location;
            if (0 < intval($price)) {
                $rv['price'] = intval($price);
                $this->addToDB($rv, $file);
                $entry[$i] = $rv;
            }
        }
        if (isset($entry)) {
            $this->em->flush();

            return $entry;
        }

        return [];
    }

    private function addToDB($newRv, $file)
    {
        $rv = new RV();
        $rv->setLocation($newRv['location'] ?? null);
        $rv->setMake($newRv['make'] ?? null);
        $rv->setPrice($newRv['price'] ?? null);
        $rv->setYear($newRv['year'] ?? null);
        $rv->setModel($newRv['model'] ?? null);
        $rv->setUrl($newRv['url'] ?? null);
        $rv->setYmm($newRv['ymm'] ?? null);
        $rv->setClass($newRv['class'] ?? null);
        $file->addRV($rv);

        $summary = $this->manageSummary($rv, $file);


        $this->em->persist($rv);
        $this->em->persist($file);
    }

    private function manageSummary($rv, $file)
    {
        $class = $rv->getClass();
        $year = $rv->getYear();
        $price = $rv->getPrice();
        $today = new \DateTime('midnight');
        $summary = $this->em->getRepository(Summary::class)->findOneBy(['added' => $today, 'class' => $class]);
        if (null === $summary) {
            $summary = new Summary();
            $summary->setAdded($today);
            $summary->setClass($class);
        }
        $summary->addFile($file);
        $this->em->persist($summary);
        $this->em->flush($summary);
        
        $getterAvgPrice = 'getYr' . $year;
        $setterAvgPrice = 'setYr' . $year;
        $getterN = 'getN' . $year;
        $setterN = 'setN' . $year;

        $currentTotal = $summary->$getterAvgPrice() * $summary->$getterN();
        $newN = $summary->$getterN() + 1;
        $summary->$setterN($newN);
        $newTotal = $currentTotal + $price;
        $newAvgPrice = round($newTotal / $newN, 0);
        $summary->$setterAvgPrice($newAvgPrice);
        $this->em->persist($summary);

        return $summary;
    }

    public function testFile()
    {
        $dir = 'G:\\workspace\\scraper\\var\\pages\\';
        $file = '20201103_rvusa.com_10-48-28' . '.html';
        $html = file_get_contents($dir . $file);
        $crawler = new Crawler($html);
        $filter = '.row.listing-top';
        $nodes = $crawler->filter($filter);
//        $filter2 = 'li[itemtype="http://schema.org/Product"]';
        // find n nodes containing rvt searches
//        $nodes = $lis->eq(2)->filter($filter2);
        $n = \count($nodes);
//        $x = $nodes->eq(0)->filter('.inv-unit-city-state');

        dd($n);
//        dd($x->text());
    }

}
