<?php

namespace App\Repository;

use App\Entity\ArticleFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ArticleFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleFile[]    findAll()
 * @method ArticleFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleFile::class);
    }

    // /**
    //  * @return ArticleFile[] Returns an array of ArticleFile objects
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

    public function setAllOld($idAuthor)
    {

    }
    /*
    public function findOneBySomeField($value): ?ArticleFile
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
