<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Summary;
use App\Service\ChartService;
use App\Service\Investigator;
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

        return $this->render('index.html.twig', [
                    'notUsed' => \count($notUsed),
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
        $a = $chart->buildChart('line', 'C', 'Price');
//        $js = $chart->getChartJs($a, 'locX');
//        dd($js);

        return $this->render('chart.html.twig', [
                    'chartA' => $a,
        ]);
    }

    /**
     * @Route("/js/{which}", name="js")
     */
    public function returnChartJs(ChartService $chart, $which)
    {
        $available = [
            ['type' => 'line', 'class' => 'C', 'subtype' => 'Price'],
            ['type' => 'line', 'class' => 'C', 'subtype' => 'Count'],
            ['type' => 'line', 'class' => 'B+', 'subtype' => 'Price'],
            ['type' => 'line', 'class' => 'B+', 'subtype' => 'Count'],
            ['type' => 'histogram', 'class' => 'C'],
            ['type' => 'histogram', 'class' => 'B+'],
        ];

        $js = $chart->getChartJs($available[$which], 'chartA');
        $response = new Response($js);

        return $response;
    }

    /**
     * @Route("/populate", name="populate")
     */
    public function populate()
    {
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository(Summary::class)->populate();
        $this->addFlash('success', $p . ' records added to Summary table');

        return $this->redirectToRoute('home');
    }

}
