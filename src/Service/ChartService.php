<?php

//src/Service/ChartService.php

namespace App\Service;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart;
use Doctrine\ORM\EntityManagerInterface;

class ChartService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function rvChart($avg, $class)
    {
//        $summary = $this->em->getRepository(Summary::class)->findAll();
        $chart = new LineChart();
        $first[] =  ['Date', '2017', '2016', '2015', '2014'];
//        foreach ($summary as $row) {
//            $table[] = [
//                $row->getSummaryDate(),
//                $row->getYr2017(),
//                $row->getYr2016(),
//                $row->getYr2015(),
//                $row->getYr2014()
//            ];
//        }

        $resultant = array_merge($first, $avg);
        $chart->getData()->setArrayToDataTable($resultant);
        $chart->getOptions()
                ->setTitle('Class ' . $class . ' RV Prices: Model Years 2014-2017')
                ->setHeight(400)
                ->setWidth(700)
                ->getLegend(['position' => 'below'])
                ;

        return $chart;
    }

}
