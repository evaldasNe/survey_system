<?php

namespace App\Repository;

use App\Entity\AttendSurvey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AttendSurvey|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttendSurvey|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttendSurvey[]    findAll()
 * @method AttendSurvey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendSurveyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttendSurvey::class);
    }

    // /**
    //  * @return AttendSurvey[] Returns an array of AttendSurvey objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttendSurvey
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
