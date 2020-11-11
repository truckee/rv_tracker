<?php

//src/Service/ChartService.php

namespace App\Service;

use App\Entity\RV;
use App\Entity\Summary;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\VAxis;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Histogram;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\LineChart as MaterialLineChart;
use Doctrine\ORM\EntityManagerInterface;

class ChartService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function rvChart($class, $type)
    {
        $data = $this->em->getRepository(Summary::class)->chartData($class, $type);
        $chart = new LineChart();
        $first[] = ['Date', '2017', '2016', '2015', '2014'];
        $resultant = array_merge($first, $data);
        $chart->getData()->setArrayToDataTable($resultant);
        $chart->getOptions()->setTitle('Class ' . $class . ' RV ' . $type . ': Model Years 2014-2017');
        $chart->getOptions()->setLineWidth(1)
                ->setHeight(400)
                ->setWidth(700)
                ->getHAxis()->setTitle('Date')->setFormat('M/d')
                ->setShowTextEvery(7);
        $chart->getOptions()->getVAxis()->setTitle(($type === 'Price') ? '$' : 'N');
        $chart->getOptions()->getLegend()->setPosition('right');

        return $chart;
    }

    public function histogram($class)
    {
        $rvs = $this->em->getRepository(RV::class)->lastFourWeeks(['class' => $class]);
        foreach ($rvs as $item) {
            $data[] = ['RV' => $item->getYmm(), 'Price' => $item->getPrice() / 1000];
        }
        $histo = new Histogram();
        $histo->getData()->setArrayToDataTable($data, true);
        $title = 'Distribution of Class ' . $class . ' RV Prices (last 4 weeks)';
        $histo->getOptions()->setTitle($title);
        $histo->getOptions()->setWidth(700);
        $histo->getOptions()->setHeight(400);
        $histo->getOptions()->getLegend()->setPosition('none');
        $histo->getOptions()->setColors(['green']);

        $histo->getOptions()->getHAxis()->setTitle('$,000');

        $vAxis1 = new VAxis();
        $vAxis1->setTitle('# RVs');
        $histo->getOptions()->setVAxes([$vAxis1]);

        return $histo;
    }

}
