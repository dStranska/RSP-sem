<?php

namespace App\Repository;

use App\Entity\ArticleTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ArticleTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleTheme[]    findAll()
 * @method ArticleTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleTheme::class);
    }

    // /**
    //  * @return ArticleTheme[] Returns an array of ArticleTheme objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArticleTheme
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
