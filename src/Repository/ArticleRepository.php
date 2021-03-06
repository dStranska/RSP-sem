<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getArticlesByAuthor(int $idUser)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.author = :val')
            ->setParameter('val', $idUser)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllWaiting()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :val')
            ->setParameter('val', 'in_progress')
            ->getQuery()
            ->getResult()
            ;
    }

    public function setReviewDone($id)
    {
        return $this->createQueryBuilder("a")->update()
            ->set('a.status', "'review_done'")
            ->where('a.id = ?1')
            ->setParameter(1, $id)
            ->getQuery()->execute();

    }

    // /**
    //  * @return Article[] Returns an array of Article objects
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
    public function findOneBySomeField($value): ?Article
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
