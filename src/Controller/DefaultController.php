<?php

//src/Controller/DefaultController.php

namespace App\Controller;

use App\Entity\File;
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
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $notUsed = $em->getRepository(File::class)->filesNotUsed();
        $used = $em->getRepository(File::class)->fileNamesUsed();
        return $this->render('index.html.twig', [
                    'notUsed' => $notUsed,
                    'used' => $used,
        ]);
    }

    /**
     * @Route("/import/{filename}", name="import_file")
     */
    public function importFile(KernelInterface $kernel, Investigator $investigate, $filename): Response
    {
        $em = $this->getDoctrine()->getManager();
        $used = $em->getRepository(File::class)->findOneBy(['filename' => $filename]);

        if (!is_null($used)) {
            $this->addFlash('warning', 'File already installed');

            return $this->redirectToRoute('home');
        } else {
            $file = new File();
            $file->setAdded(new \DateTime(substr($filename, 0, 8)));
            $file->setFilename($filename);
            $em->persist($file);
            $em->flush();
        }

        $projectDir = $kernel->getProjectDir();
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
