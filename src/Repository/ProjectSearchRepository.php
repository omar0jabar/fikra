<?php

namespace App\Repository;

use App\Entity\ProjectSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectSearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectSearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectSearch[]    findAll()
 * @method ProjectSearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectSearchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectSearch::class);
    }

    // /**
    //  * @return ProjectSearch[] Returns an array of ProjectSearch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectSearch
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
