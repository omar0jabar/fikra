<?php

namespace App\Repository;

use App\Entity\LogDownload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogDownload|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogDownload|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogDownload[]    findAll()
 * @method LogDownload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogDownloadRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogDownload::class);
    }

    // /**
    //  * @return LogDownload[] Returns an array of LogDownload objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogDownload
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
