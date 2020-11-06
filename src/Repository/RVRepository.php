<?php

namespace App\Repository;

use App\Entity\File;
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

    /*
     * Create rows of data for charting
     */

//    public function averagePrice($class, $counts = false)
//    {
//        $modelYears = [
//            '2017',
//            '2016',
//            '2015',
//            '2014',
//        ];
//
//        $dates = $this->createQueryBuilder('r')
//                ->select('f.added Added')
//                ->distinct()
//                ->from(File::class, 'f')
//                ->getQuery()
//                ->getResult();
//
//        if (true === $counts) {
//            $qb = $this->createQueryBuilder('r')
//                    ->select('r.year Year,  AVG(r.price) Price, COUNT(r.year) N')
//                    ->join('r.file', 'f')
//                    ->where('r.year IN(:modelYears)')
//                    ->andWhere('r.class = :class')
//                    ->andWhere('f.added = :added')
//            ;
//        } else {
//            $qb = $this->createQueryBuilder('r')
//                    ->select('r.year Year,  AVG(r.price) Price')
//                    ->join('r.file', 'f')
//                    ->where('r.year IN(:modelYears)')
//                    ->andWhere('r.class = :class')
//                    ->andWhere('f.added = :added')
//            ;
//        }
//
//        foreach ($dates as $added) {
//            $row[] = $added['Added'];
//            $query = $qb
//                    ->orderBy('r.year', 'DESC')
//                    ->groupBy('Year')
//                    ->setParameters(['modelYears' => $modelYears, 'class' => $class, 'added' => $added['Added']])
//                    ->getQuery()
//                    ->getArrayResult();
//
//            foreach ($query as $year) {
//                $row[] = round($year['Price'], 0);
//            }
//            if ($counts) {
//                foreach ($query as $year) {
//                    $row[] = intval($year['N']);
//                }
//            }
//
//            $averages[] = $row;
//            unset($row);
//        }
//
//        return $averages;
//    }

}
