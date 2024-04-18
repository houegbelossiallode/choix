<?php

namespace App\Repository;

use App\Entity\Attestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attestation>
 *
 * @method Attestation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attestation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attestation[]    findAll()
 * @method Attestation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attestation::class);
    }

//    /**
//     * @return Attestation[] Returns an array of Attestation objects
//     */
    public function findByUser($etudiantId): array
    {
        $query = $this->createQueryBuilder('a')
        ->innerJoin('a.etudiant','u')
        ->where('u.id = :etudiantId')
        ->setParameter('etudiantId',$etudiantId)
        ->getQuery()
        ->getResult();

        return $query;
    }


    public function getNb()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a)');
        return(int) $qb->getQuery()->getSingleScalarResult();
    }
    

//    public function findOneBySomeField($value): ?Attestation
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}