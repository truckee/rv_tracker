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

    public function scrape($dir, $filename) {

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

    private function addToDB($newRv, $filename) {
        $rv = new RV();
        $rv->setAdLocation($newRv['ad_location']);
        $rv->setAdMake($newRv['ad_make']);
        $rv->setAdPrice($newRv['ad_price']);
        $rv->setAdYear($newRv['ad_year']);
        $rv->setAdModel($newRv['ad_model']);
        $rv->setUrl($newRv['url']);
        $rv->setYmm($newRv['ymm']);
        $rv->setFilename($filename);
        $em = $this->em;
        $em->persist($rv);
    }

}
