<?php

namespace App\Repository;

use App\Entity\CommentSaMarcheBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CommentSaMarcheBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentSaMarcheBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentSaMarcheBlock[]    findAll()
 * @method CommentSaMarcheBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentSaMarcheBlockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentSaMarcheBlock::class);
    }

    // /**
    //  * @return CommentSaMarcheBlock[] Returns an array of CommentSaMarcheBlock objects
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
    public function findOneBySomeField($value): ?CommentSaMarcheBlock
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
