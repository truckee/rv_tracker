<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Finder\Finder;

/**
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function fileNamesUsed()
    {
        $fileNames = $this->createQueryBuilder('f')
                ->select('f')
                ->orderBy('f.filename', 'DESC')
                ->getQuery()
                ->getArrayResult()
        ;
        if (empty($fileNames)) {
            $files = [];
        }
        foreach ($fileNames as $arr) {
            $files[] = $arr['filename'];
        }

        return $files;
    }

    public function mostRecent()
    {
        $files = $this->createQueryBuilder('f')
                ->select('f.filename')
                ->join('f.dates', 's')
                ->orderBy('s.added', 'DESC')
                ->addOrderBy('f.filename')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
        ;

        return $files;
    }

    public function filesNotUsed($path)
    {
        $used = $this->fileNamesUsed();
        $finder = new Finder();
        $files = $finder->in($path);
        foreach ($files as $item) {
            $testName = $item->getFilename();
            if (!in_array($testName, $used)) {
                $names[] = $testName;
            }
        }

        return (isset($names)) ? $names : [];
    }

    // /**
    //  * @return File[] Returns an array of File objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('f')
      ->andWhere('f.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('f.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?File
      {
      return $this->createQueryBuilder('f')
      ->andWhere('f.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
