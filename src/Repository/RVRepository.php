<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\RV;
use App\Entity\Summary;
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

    public function lastFourWeeks($class)
    {
        $then = new \DateTIme('4 weeks ago');
        $qb = $this->createQueryBuilder('r');

        return $this->createQueryBuilder('r')
                        ->join('r.file', 'f')
                        ->join('f.dates', 's')
                        ->where($qb->expr()->gte('s.added', '?1'))
                        ->andWhere('r.class = :class')
                        ->setParameter(1, $then)
                        ->setParameter('class', $class)
                        ->getQuery()->getResult();
    }

}
