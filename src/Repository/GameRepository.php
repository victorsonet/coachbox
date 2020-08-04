<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.slug = :val')
            // ->leftJoin('c.genres', 'g')
            // ->addSelect('g')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByTerm($term)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.game LIKE :val')
            ->setParameter('val', '%'.$term.'%' )
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
