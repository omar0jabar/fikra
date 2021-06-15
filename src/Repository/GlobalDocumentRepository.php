<?php

namespace App\Repository;

use App\Entity\GlobalDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GlobalDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalDocument[]    findAll()
 * @method GlobalDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalDocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GlobalDocument::class);
    }

    public function findByCritere($type, $date, $q, $offset, $limit)
    {
       
       
        $query = $this->createQueryBuilder('d')
                ->andWhere('d.type = :type')
                ->setParameter('type', $type);

        if (!empty($q)) {
            $query->andWhere('d.title LIKE :title')
            ->setParameter('title', '%'.$q.'%');
        }
        if (!empty($date)) {
            $date_start = $date.'-01-01';
            $date_end = $date.'-12-31';
            $query->andWhere('d.date BETWEEN :from AND :to')
                    ->setParameter('from', $date_start )
                    ->setParameter('to', $date_end);
        }

        return $query->orderBy('d.title', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
