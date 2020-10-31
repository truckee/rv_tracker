<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Summary;
use App\Service\ChartService;
use App\Service\Investigator;
//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    private $projectDir;

    public function __construct($projectDir) {
        $this->projectDir = $projectDir;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(ChartService $chart)
    {
        $rvData = $chart->rvChart();
        $em = $this->getDoctrine()->getManager();
        $path = '../var/pages';
        $notUsed = $em->getRepository(File::class)->filesNotUsed($path);
        $used = $em->getRepository(File::class)->mostRecent();

        return $this->render('index.html.twig', [
                    'notUsed' => $notUsed,
                    'used' => $used,
                    'rvData' => $rvData,
        ]);
    }

    /**
     * @Route("/import/{filename}", name="import_file")
     */
    public function importFile(Investigator $investigate, $projectDir, $filename): Response
    {
        $em = $this->getDoctrine()->getManager();
        $used = $em->getRepository(File::class)->findOneBy(['filename' => $filename]);

        if (!is_null($used)) {
            $this->addFlash('warning', 'File already installed');

            return $this->redirectToRoute('home');
        } else {
            $file = new File();
            $added = new \DateTime(substr($filename, 0, 8));
            $file->setAdded($added);
            $file->setFilename($filename);
            $em->persist($file);

            $entity = $em->getRepository(Summary::class)->findOneBy(['summary_date' => $added]);
            if (null === $entity) {
                $summary = new Summary();
                $summary->setSummaryDate($added);
                $em->persist($summary);
            }

            $em->flush();
        }

        $dir = $projectDir . '\\var\pages\\';

        $start = strpos($filename, '_') + 1;
        $end = strpos($filename, '.');
        $len = $end - $start;
        $source = substr($filename, $start, $len);
        $rvs = $investigate->$source($dir, $file);

        return $this->render('rv.html.twig', [
                    'rvs' => $rvs,
        ]);
    }

}
