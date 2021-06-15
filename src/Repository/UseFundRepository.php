<?php

namespace App\Repository;

use App\Entity\UseFund;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UseFund|null find($id, $lockMode = null, $lockVersion = null)
 * @method UseFund|null findOneBy(array $criteria, array $orderBy = null)
 * @method UseFund[]    findAll()
 * @method UseFund[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UseFundRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UseFund::class);
    }

}
