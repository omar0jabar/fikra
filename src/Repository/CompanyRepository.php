<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * @param User $author
     * @return mixed
     */
    public function findAllByStartupNotDeleted(User $author)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isDeleted = 0')
            ->andWhere('c.user = :author')
            ->setParameter('author', $author)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
