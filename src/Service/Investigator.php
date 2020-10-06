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

    public function rvt($dir, $filename) {
        $fullName = $dir . $filename;
        $html = file_get_contents($fullName);

        $crawler = new Crawler($html);
        $filter = 'ul#search-results.search-results';
        $divs = $crawler->filter($filter);
        $found = $divs->html();
        // need to step over malformed html
        file_put_contents('../var/pages/found.html', $found);
        $crawler2 = new Crawler($found);
        $filter2 = 'li[itemtype="http://schema.org/Product"]';
        $lis = $crawler2->filter($filter2);
        $n = count($lis);
        for ($i = 0; $i < $n; $i++) {
            $html = $lis->eq($i)->html();
            dd($html);
            foreach ($li2 as $value) {
                
            }
        }
//        dd($divs->html());
        dd(count($li2));
//        dd($divs->eq(0)->text());
    }

    public function rvtrader($dir, $filename) {
        $fullName = $dir . $filename;
        $html = file_get_contents($fullName);

        $dataAttr = [
            'ymm',
            'url',
            'ad_make',
            'ad_model',
            'ad_price',
            'ad_location',
            'ad_year',
        ];
        $crawler = new Crawler($html);
        $filter = "div.margin-bottom30.bgWhite.boxShadow:nth-child(1)";
        $divs = $crawler->filter($filter);
        $n = count($divs);
        for ($i = 0; $i < $n; $i++) {
            $html = $divs->eq($i)->html();
            foreach ($dataAttr as $value) {
                $attr = 'data-' . $value;
                $len = strlen($attr . '="');
                $pos = strpos($html, $attr);
                $start = $pos + $len;
                $end = stripos($html, '"', $start);
                $item = substr($html, $start, $end - $start);
                $rv[$value] = $item;
            }
            $this->addToDB($rv, $filename);
            $entry[$i] = $rv;
        }
        $this->em->flush();

        return $entry;
    }

    public function scrape($dir, $filename) {
        
    }

    private function addToDB($newRv, $filename) {
        $rv = new RV();
        $rv->setLocation($newRv['location']);
        $rv->setMake($newRv['make']);
        $rv->setPrice($newRv['price']);
        $rv->setYear($newRv['year']);
        $rv->setModel($newRv['model']);
        $rv->setUrl($newRv['url']);
        $rv->setYmm($newRv['ymm']);
        $rv->setFilename($filename);
        $em = $this->em;
        $em->persist($rv);
    }

}
