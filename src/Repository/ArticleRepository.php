<?php

namespace App\Repository;

use App\Entity\Article;
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
        return $this->createQueryBuilder('a')
            ->where('a.texte LIKE :string');

        if ($request->get('themes')) {
            foreach (($request->get('themes')) as $value) {
                $this->orWhere('a.theme = (:value)')
                    ->setParameter('value', $value->getId());
            }
        }
        if ($request->get('pays')) {
            foreach (($request->get('pays')) as $value) {
                $this->orWhere('a.pays = (:value)')
                    ->setParameter('value', $value->getId());
            }
        }
        // ->join('a.auteurArticle', 'u')
        // ->join('a.hashtags', 'h')
        $this->orWhere('a.lieu LIKE :string')
            // ->orWhere('u.pseudo LIKE :string')
            ->orWhere('a.titre LIKE :string')
            // ->orWhere('h.name LIKE :string')
            ->setParameter('string', '%' . $request->get('search') . '%')
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
