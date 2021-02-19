<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Summary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Summary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Summary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Summary[]    findAll()
 * @method Summary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SummaryRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Summary::class);
    }

    public function chartData($class, $type)
    {
        $columns = 's.added, ';
        switch ($type) {
            case 'Price':
                $columns .= 's.yr_2020, s.yr_2019, s.yr_2018, s.yr_2017, s.yr_2016, s.yr_2015, s.yr_2014';
                break;
            case 'Count':
                $columns .= 's.n_2020, s.n_2019, s.n_2018, s.n_2017, s.n_2016, s.n_2015, s.n_2014';
                break;
            default:
                break;
        }

        $query = $this->createQueryBuilder('s')
                        ->select($columns)
                        ->where('s.class = :class')
                        ->andWhere('s.added >= :date')
                        ->orderBy('s.added')
                        ->setParameter('class', $class)
                        ->setParameter('date', '2021-02-06')
                        ->getQuery()->getResult();
        foreach ($query as $row) {
            foreach ($row as $key => $value) {
                if (!is_object($value)) {
                    $newRow[$key] = floatval($value);
                } else {
                    $newRow[$key] = $value;
                }
            }
            $resultant[] = $newRow;
        };

        return $resultant;
    }

    /*
     * This is retained for historical interest only. Originally used
     * to update summary table
     */

    public function populate()
    {
        $history = $this->getEntityManager()->createQuery(
                        "select s.added, r.year, (sum(r.price)/count(r.year)) average, count(r.year) N from App\Entity\RV r
                join App\Entity\File f with f = r.file
                join App\Entity\Summary s with s = f.dates
                where r.year in ('2020', '2019', '2018', '2017', '2016', '2015', '2014')
                group by s.added, r.year
                order by s.added, r.year desc"
                )->getArrayResult();

        // $history ~ [date, year, average, N]
        $i = 0;
        foreach ($history as $item) {
            $qb = $this->createQueryBuilder('s')
                            ->update()
                            ->set('s.yr_' . $item['year'], '?1')
                            ->set('s.n_' . $item['year'], '?2')
                            ->where('s.added = ?3')
                            ->setParameter(1, floatval($item['average']))
                            ->setParameter(2, $item['N'])
                            ->setParameter(3, $item['added'])
                            ->getQuery()->execute();
            $i++;
        }

        return $i;
    }

    // /**
    //  * @return Summary[] Returns an array of Summary objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('s.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Summary
      {
      return $this->createQueryBuilder('s')
      ->andWhere('s.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
