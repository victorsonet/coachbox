<?php

namespace App\Repository;

use App\Entity\Coach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coach|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coach|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coach[]    findAll()
 * @method Coach[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coach::class);
    }

      /**
      * @return Coach[] Returns an array of Coach objects
      */
   
    public function findByTerm($term)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.first_name LIKE :val OR c.last_name LIKE :val')
            ->setParameter('val', '%'.$term.'%' )
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.slug = :val')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
