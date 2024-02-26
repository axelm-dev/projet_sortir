<?php

namespace App\Repository;

use App\Entity\Meeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meeting>
 *
 * @method Meeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meeting[]    findAll()
 * @method Meeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meeting::class);
    }

    /**
     * @return Meeting[]|null
     */
    public function findAllOrderByDate(): ?array
    {
        return $this->createQueryBuilder('m')
            ->join('m.state', 's')
            ->join('m.organizer', 'o')
            ->andWhere('s.value != :archivingState')
            ->setParameter('archivingState', 'Archivée')
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Meeting[] Returns an array of Meeting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Meeting
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findMeetingByFilter(mixed $data, $userId)
    {

        $query = $this->createQueryBuilder('m')
            ->join('m.campus', 'c')
            ->join('m.state', 's')
            ->join('m.participants', 'p');

        if (!empty($data['campus'])) {
            $query->andWhere('c.id = :campus')
                ->setParameter('campus', $data['campus']);
        }

        if (!empty($data['search'])) {
            $query->andWhere('m.name LIKE :search')
                ->setParameter('search', '%'.$data['search'].'%');
        }

        if (!empty($data['start_date']) && empty($data['end_date'])) {
            $format_date_start = date_format($data['start_date'], 'Y-m-d H:i:s');
            $query->andWhere('m.date >= :start_date')
                ->setParameter('start_date', $format_date_start);
        }

        if (!empty($data['end_date']) && empty($data['start_date'])) {
            $format_date_end = date_format($data['end_date'], 'Y-m-d H:i:s');
            $query->andWhere('m.date <= :end_date')
                ->setParameter('end_date', $format_date_end);
        }

        if(!empty($data['start_date']) && !empty($data['end_date'])) {
            $format_date_start = date_format($data['start_date'], 'Y-m-d H:i:s');
            $format_date_end = date_format($data['end_date'], 'Y-m-d H:i:s');
            $query->andWhere('m.date BETWEEN :start_date AND :end_date')
                ->setParameter('start_date', $format_date_start)
                ->setParameter('end_date', $format_date_end);
        }

        if(!empty($data['organisateur'])) {
            $query->andWhere('m.organizer = :organisateur')
                ->setParameter('organisateur', $userId);
        }

        if(!empty($data['inscrit'])) {
            $query
                ->andWhere('p.id IN (:inscrit)')
                ->setParameter('inscrit', $userId);
        }

        if(!empty($data['non_inscrit'])) {
            $query
                ->andWhere('p.id NOT IN(:non_inscrit)')
                ->setParameter('non_inscrit', $userId);
        }

        if (!empty($data['state'])) {
            $query->andWhere('s.value = :state')
                ->setParameter('state', 'Passée');
        }

        return $query->getQuery()->getResult();
    }
}
