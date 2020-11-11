<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\File;
//use App\Entity\RV;
use App\Entity\Summary;
use App\Service\ChartService;
use App\Service\Investigator;
//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{

    private $investigator;
    private $projectDir;
    private $path = '../var/pages';

    public function __construct(Investigator $investigator, $projectDir)
    {
        $this->investigator = $investigator;
        $this->projectDir = $projectDir;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(ChartService $chart)
    {
        $em = $this->getDoctrine()->getManager();
        $notUsed = $em->getRepository(File::class)->filesNotUsed($this->path);
        $used = $em->getRepository(File::class)->mostRecent();
        $priceC = $chart->rvChart('C', 'Price');
        $countC = $chart->rvChart('C', 'Count');
        $priceB = $chart->rvChart('B+', 'Price');
        $countB = $chart->rvChart('B+', 'Count');
        $histoC = $chart->histogram('C');
        $histoB = $chart->histogram('B+');

        return $this->render('index.html.twig', [
                    'notUsed' => $notUsed,
                    'used' => $used,
                    'priceC' => $priceC,
                    'priceB' => $priceB,
                    'countC' => $countC,
                    'countB' => $countB,
                    'histoC' => $histoC,
                    'histoB' => $histoB,
        ]);
    }

    /**
     * @Route("/importFile/{filename}", name="import_file")
     */
    public function importFile($filename): Response
    {
        $em = $this->getDoctrine()->getManager();
        $used = $em->getRepository(File::class)->findOneBy(['filename' => $filename]);

        if (!is_null($used)) {
            $this->addFlash('warning', 'File already installed');

            return $this->redirectToRoute('home');
        } else {
            $rvs = $this->loadFile($filename);
        }

        return $this->render('rv.html.twig', [
                    'rvs' => $rvs,
        ]);
    }

    /**
     * @Route("/loadAll", name="load_all")
     */
    public function loadAll()
    {
        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository(File::class)->filesNotUsed($this->path);
        $n = 0;
        foreach ($files as $import) {
            $rvs = $this->loadFile($import);
            $n += \count($rvs);
        }
        $this->addFlash('success', $n . ' RVs loaded');

        return $this->redirectToRoute('home');
    }

    private function loadFile($filename)
    {
        $em = $this->getDoctrine()->getManager();

        $file = new File();
        $file->setFilename($filename);
        $em->persist($file);

        $dir = $this->projectDir . '\\var\pages\\';
        $start = strpos($filename, '_') + 1;
        $end = strpos($filename, '.');
        $len = $end - $start;
        $source = substr($filename, $start, $len);
        $rvs = $this->investigator->$source($dir, $file);

        return $rvs;
    }

    /**
     * @Route("exp", name="exp")
     */
    public function experiment(ChartService $chart)
    {
//        $this->investigator->testFile();
//        $em = $this->getDoctrine()->getManager();
//        $countDataBPlus = $em->getRepository(Summary::class)->chartData('count', 'B+');
//        dump($countDataBPlus);
        $test = $chart->lineChart();
//        $test = $chart->rvChart('B+', 'Count');
//        dump($test);
//        $chart = $chart->histogram('B+');

        return $this->render('chart.html.twig', [
                    'chart' => $test
        ]);
    }

}
