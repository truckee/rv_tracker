<?php

namespace App\Repository;

use App\Entity\RV;
use Doctrine\ORM\QueryBuilder;
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
        return $this->fourWeeks($class)
                        ->getQuery()->getResult();
    }

    public function bPlus($price)
    {
        $then = new \DateTIme('4 weeks ago');
        $qb = $this->createQueryBuilder('r');

        return $this->createQueryBuilder('r')
                        ->select('r.ymm, r.price')
                        ->distinct()
                        ->join('r.file', 'f')
                        ->join('f.dates', 's')
                        ->where($qb->expr()->gte('s.added', '?1'))
                        ->andWhere('r.class = :class')
                        ->andWhere($qb->expr()->lte('r.price', ':price'))
                        ->orderBy('r.ymm', 'ASC')
                        ->orderBy('r.price', 'DESC')
                        ->setParameter(1, $then)
                        ->setParameter('class', 'B+')
                        ->setParameter('price', $price)
                        ->getQuery()->getResult();
    }

    public function listCompare($list)
    {
        foreach ($list as $item) {
            $qb = $this->createQueryBuilder('r')
                            ->select('COUNT(r) N, SUM(r.price) Total ')
                            ->where('r.ymm LIKE :item')
                            ->setParameter('item', '%' . $item . '%')
                            ->getQuery()->getResult();
            if ('0' !== $qb[0]['N']) {
                $found[] = array_merge(['name' => $item], ['N' => (int) $qb[0]['N'], 'Avg' => round($qb[0]['Total'] / $qb[0]['N'], 0)]);
            }
        }

        return $found;
    }

    private function fourWeeks($class)
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
        ;
    }

    private function addPriceLimit(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
                        ->andWhere('r.price <= 60000');
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('q');
    }

}
