<?php

namespace App\Repository;

use App\Entity\StateMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StateMeeting>
 *
 * @method StateMeeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method StateMeeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method StateMeeting[]    findAll()
 * @method StateMeeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateMeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StateMeeting::class);
    }

//    /**
//     * @return StateMeeting[] Returns an array of StateMeeting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StateMeeting
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
