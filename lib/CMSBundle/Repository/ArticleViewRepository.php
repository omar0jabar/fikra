<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\ArticleView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArticleView|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleView|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleView[]    findAll()
 * @method ArticleView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleViewRepository extends ServiceEntityRepository
{
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, ArticleView::class);
   }

}
