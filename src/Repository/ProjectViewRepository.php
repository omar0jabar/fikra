<?php

namespace App\Repository;

use App\Entity\ProjectView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectView|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectView|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectView[]    findAll()
 * @method ProjectView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectViewRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectView::class);
    }

    // /**
    //  * @return ProjectView[] Returns an array of ProjectView objects
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
    public function findOneBySomeField($value): ?ProjectView
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
