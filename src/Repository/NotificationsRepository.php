<?php

namespace App\Repository;

use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notifications>
 *
 * @method Notifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifications[]    findAll()
 * @method Notifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifications::class);
    }

//    /**
//     * @return Notifications[] Returns an array of Notifications objects
//     */
        public function findByNotification($id): array
        {
            $query = $this->createQueryBuilder('n')
            ->innerJoin('n.stage','s')
            ->where('s.etudiant = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
        return $query;
        }

        public function getNb($id)
        {
            $qb = $this->createQueryBuilder('n')
                ->select('COUNT(n)')
                ->innerJoin('n.stage','s')
                ->where('s.etudiant = :id')
                ->setParameter('id',$id);
            return(int) $qb->getQuery()->getSingleScalarResult();
           
        }









        
//    public function findOneBySomeField($value): ?Notifications
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}