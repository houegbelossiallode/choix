<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

//    /**
//     * @return Messages[] Returns an array of Messages objects
//     */
   public function findByvalide()
    {
        $qb = $this->createQueryBuilder('m')
            ->update()
            ->set('m.statut', true)
            ->orderBy('m.id', 'ASC');
            
            return $qb;
    }
    public function findByMessage($id): array
    {
        $query = $this->createQueryBuilder('m')
        ->innerJoin('m.offre','o')
        ->where('o.entreprise = :id')
        ->setParameter('id',$id)
        ->getQuery()
        ->getResult();
       return $query;
    }




    public function getNb($id)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->innerJoin('m.offre','o')
            ->where('o.entreprise = :id')
            ->setParameter('id',$id);
        return(int) $qb->getQuery()->getSingleScalarResult();
       
    }





    
    
}