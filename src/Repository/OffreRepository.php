<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 *
 * @method Offre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offre[]    findAll()
 * @method Offre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

//    /**
//     * @return Offre[] Returns an array of Offre objects
//     */
        public function findByUser($entrepriseId): array
        {
            $query = $this->createQueryBuilder('o')
            ->innerJoin('o.entreprise','u')
            ->where('u.id = :entrepriseId')
            ->setParameter('entrepriseId',$entrepriseId)
            ->getQuery()
            ->getResult();
        return $query;
        }

        public function getNb()
        {
            $qb = $this->createQueryBuilder('o')
                ->select('COUNT(o)');
            return(int) $qb->getQuery()->getSingleScalarResult();
        }
        
        public function findByvalide()
        {
            $qb = $this->getEntityManager()->createQueryBuilder()
                ->update('o')
                ->set('validated', 'unread')
                
                ->getQuery();
             // $qb->exec();
            
        }



    
}