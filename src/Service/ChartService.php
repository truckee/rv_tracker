<?php

//src/Service/ChartService.php

namespace App\Service;

use App\Entity\RV;
use CMEN\GoogleChartsBundle\GoogleCharts\Options\VAxis;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Histogram;
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
        $chart = new LineChart();
        $first[] = ['Date', '2017', '2016', '2015', '2014'];
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

    public function histogram()
    {
        $rvs = $this->em->getRepository(RV::class)->findBy(['class' => 'C']);
        foreach ($rvs as $item) {
            $data[] = ['RV' => $item->getYmm(), 'Price' => $item->getPrice() / 1000];
        }
        $histo = new Histogram();
        $histo->getData()->setArrayToDataTable($data, true);

        $histo->getOptions()->setTitle('Distribution of RV Prices');
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
