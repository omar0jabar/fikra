<?php

namespace App\Repository;

use App\Entity\CompanyFAQ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompanyFAQ|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyFAQ|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyFAQ[]    findAll()
 * @method CompanyFAQ[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyFAQRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyFAQ::class);
    }

    // /**
    //  * @return CompanyFAQ[] Returns an array of CompanyFAQ objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyFAQ
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
