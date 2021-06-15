<?php

namespace App\Repository;

use App\Entity\CompanyLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompanyLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyLike[]    findAll()
 * @method CompanyLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyLikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyLike::class);
    }

}
