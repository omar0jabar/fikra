<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getAllQuery(Project $project)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
    }

    public function getAllQueryByInvestor(User $user)
    {
        return $this->createQueryBuilder('m')
            ->join("m.project", "p")
            ->andWhere('m.author = :author')
            ->orWhere('p.author = :author')
            ->andWhere('p.isDeleted = 0')
            ->setParameter('author', $user)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
    }

    public function getAllQueryByInvestor0(User $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.author = :author')
            ->setParameter('author', $user)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
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
    public function findOneBySomeField($value): ?Message
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
