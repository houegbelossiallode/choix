<?php

namespace App\Repository;

use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stage>
 *
 * @method Stage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stage[]    findAll()
 * @method Stage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stage::class);
    }

//    /**
//     * @return Stage[] Returns an array of Stage objects
//     */
    public function findByUser($etudiantId): array
    {
        $query = $this->createQueryBuilder('s')
        ->innerJoin('s.etudiant','u')
        ->where('u.id = :etudiantId')
        ->setParameter('etudiantId',$etudiantId)
        ->getQuery()
        ->getResult();
    return $query;
    }


    public function getNb()
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(s)');
        return(int) $qb->getQuery()->getSingleScalarResult();
    }
    

    public function findByEntreprise($entrepriseId): array
    {
        $query = $this->createQueryBuilder('s')
        ->innerJoin('s.entreprise','u')
        ->where('u.id = :entrepriseId')
        ->setParameter('entrepriseId',$entrepriseId)
        ->getQuery()
        ->getResult();
        return $query;
    }



   






    
}