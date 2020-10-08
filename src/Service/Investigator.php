<?php

//src/Service/Crawler.php

namespace App\Service;

use App\Entity\RV;
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

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function rvt($dir, $file) {
        $fullName = $dir . $file->getFilename();
        $html = file_get_contents($fullName);

        $crawler = new Crawler($html);
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
                $rv['ymm'] = $x->filter('h5')->text();
                $rv['price'] = preg_replace("/[^0-9]/", '', $price);
                $rv['location'] = $x->filter('span.location')->text();
                $this->addToDB($rv, $file);
                $entry[$i] = $rv;
            }
        }
        $this->em->flush();

        return $entry;
    }

    public function rvtrader($dir, $file) {
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
        $divs = $crawler->filter($filter);
        $n = count($divs);
        for ($i = 0; $i < $n; $i++) {
            $html = $divs->eq($i)->html();
            foreach ($fieldMap as $key => $value) {
                $attr = $key;
                $len = strlen($attr . '="');
                $pos = strpos($html, $attr);
                $start = $pos + $len;
                $end = stripos($html, '"', $start);
                
                $item = substr($html, $start, $end - $start);
                $rv[$value] = ($value === 'url') ? 'https://www.rvtrader.com' . $item : $item;
            }
            $this->addToDB($rv, $file);
            $entry[$i] = $rv;
        }
        $this->em->flush();

        return $entry;
    }

    public function import($filename) {
        return 'Not my job, ' . $filename;
    }
    
    private function addToDB($newRv, $file) {
        $rv = new RV();
        $rv->setLocation($newRv['location'] ?? null);
        $rv->setMake($newRv['make'] ?? null);
        $rv->setPrice($newRv['price'] ?? null);
        $rv->setYear($newRv['year'] ?? null);
        $rv->setModel($newRv['model'] ?? null);
        $rv->setUrl($newRv['url'] ?? null);
        $rv->setYmm($newRv['ymm'] ?? null);
        $file->addRV($rv);

        $this->em->persist($rv);
        $this->em->persist($file);
    }

}
