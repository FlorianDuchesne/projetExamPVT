<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Hashtag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findBySearch($request)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.texte LIKE :string')
            ->orWhere('a.lieu LIKE :string')
            ->orWhere('a.titre LIKE :string')
            ->andWhere('a.statut = 1');

        if ($request->get('themes')) {
            foreach (($request->get('themes')) as $value) {
                $qb->andWhere('a.theme = (:value)')
                    ->setParameter('value', $value);
            }
        }
        if ($request->get('pays')) {
            foreach (($request->get('pays')) as $value) {
                $qb->andWhere('a.pays = (:value)')
                    ->setParameter('value', $value);
            }
        }
        if ($request->get('tag')) {
            // dd($request->get('tag'));
            foreach (($request->get('tag')) as $value) {
                // dump($value);
                $qb->andWhere(":value MEMBER OF a.hashtags")
                    ->setParameter("value", $value);
            }
        }

        return $qb
            ->setParameter('string', '%' . $request->get('search') . '%')
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByLieu($string)
    {
        return $this->createQueryBuilder('a')
            ->where(':string = a.lieu')
            ->andWhere('a.statut = 1')
            ->setParameter('string', $string)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByTag($tag)
    {
        return $this->createQueryBuilder('a')
            ->where(':tag MEMBER OF a.hashtags')
            ->andWhere('a.statut = 1')
            ->setParameter('tag', $tag)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByFollow($following, $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.statut = 1')
            ->andWhere('a.auteurArticle IN (:following) OR a.auteurArticle IN (:user)')
            ->setParameter('following', $following)
            ->setParameter('user', $user)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByAuteurArticleAndStatut($user)
    {
        return $this->createQueryBuilder('a')
            // ->where('a.statut = 1')
            ->where('a.auteurArticle = (:val)')
            ->setParameter('val', $user)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
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
