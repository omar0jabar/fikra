<?php

namespace App\Repository;

use App\Entity\ApprovedUseFund;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedUseFund|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedUseFund|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedUseFund[]    findAll()
 * @method ApprovedUseFund[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedUseFundRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedUseFund::class);
    }

}
