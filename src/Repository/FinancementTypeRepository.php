<?php

namespace App\Repository;

use App\Entity\FinancementType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FinancementType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FinancementType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FinancementType[]    findAll()
 * @method FinancementType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinancementTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FinancementType::class);
    }

    // /**
    //  * @return FinancementType[] Returns an array of FinancementType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FinancementType
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
