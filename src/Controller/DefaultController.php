<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\RV;
use App\Service\Investigator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index() {
        $finder = new Finder();
        $files = $finder->in('../var/pages');
        $em = $this->getDoctrine()->getManager();
        $used = $em->getRepository(RV::class)->filesUsed();
        return $this->render('index.html.twig', [
            'files' => $files,
            'used' => $used,
        ]);
    }

    /**
     * @Route("/rvt/{filename}", name="rvt")
     */
    public function rvt(KernelInterface $kernel, Investigator $investigate, $filename): Response {
        $em = $this->getDoctrine()->getManager();
        $used = $em->getRepository(RV::class)->filesUsed();

        if (in_array($filename, $used)) {
            $this->addFlash('warning', 'File already installed');

            return $this->redirectToRoute('home');
        }
        
        $projectDir = $kernel->getProjectDir();
        $dir =  $projectDir . '\\var\pages\\';

        $rvs = $investigate->scrape($dir, $filename);

        return $this->render('rv.html.twig', [
                    'rvs' => $rvs,
        ]);
    }

}
