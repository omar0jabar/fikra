<?php

namespace App\Repository;

use App\Entity\MailSended;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MailSended|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailSended|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailSended[]    findAll()
 * @method MailSended[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailSendedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MailSended::class);
    }

    // /**
    //  * @return MailSended[] Returns an array of MailSended objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MailSended
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
