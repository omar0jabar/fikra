<?php

namespace App\Repository;

use App\Entity\BusinessModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BusinessModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusinessModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusinessModel[]    findAll()
 * @method BusinessModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessModelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BusinessModel::class);
    }

    // /**
    //  * @return BusinessModel[] Returns an array of BusinessModel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BusinessModel
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
