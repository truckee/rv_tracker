<?php

//src/path_here/AutoFileImport.php

namespace App\Command;

use App\Entity\File;
use App\Service\Investigator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutoFileImportCommand extends Command
{

    protected static $defaultName = 'app:import-files';
    private $em;
    private $investigator;
    private $projectDir;

    public function __construct(EntityManagerInterface $em, Investigator $investigator, $projectDir) {
        $this->em = $em;
        $this->investigator = $investigator;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure() {
        $this->setDescription('Imports data from newly added html files');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $path = './var/pages';
        $files = $this->em->getRepository(File::class)->filesNotUsed($path);
        foreach ($files as $import) {
            $file = new File();
            $file->setAdded(new \DateTime(substr($import, 0, 8)));
            $file->setFilename($import);
            $this->em->persist($file);
            $this->em->flush();
            $dir = $this->projectDir . '\\var\pages\\';

            $start = strpos($import, '_') + 1;
            $end = strpos($import, '.');
            $len = $end - $start;
            $source = substr($import, $start, $len);
            $this->investigator->$source($dir, $file);
        }

        $final = $this->em->getRepository(File::class)->filesNotUsed($path);
        if (!empty($final)) {
            return Command::FAILURE;
        } else {
            return Command::SUCCESS;
        }
    }

}
