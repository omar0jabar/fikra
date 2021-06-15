<?php

namespace App\Repository;

use App\Entity\ApprovedDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedDocument[]    findAll()
 * @method ApprovedDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedDocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedDocument::class);
    }

    // /**
    //  * @return ApprovedDocument[] Returns an array of ApprovedDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApprovedDocument
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
