<?php

namespace App\Repository;

use App\Entity\RV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RV|null find($id, $lockMode = null, $lockVersion = null)
 * @method RV|null findOneBy(array $criteria, array $orderBy = null)
 * @method RV[]    findAll()
 * @method RV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RV::class);
    }

    // /**
    //  * @return RV[] Returns an array of RV objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('r')
      ->andWhere('r.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('r.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?RV
      {
      return $this->createQueryBuilder('r')
      ->andWhere('r.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
