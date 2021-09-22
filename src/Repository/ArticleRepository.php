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
        // On crée une query qui retournera un ou des objets articles
        $qb = $this->createQueryBuilder('a')
            // Où le texte, lieu ou titre de l'article contient l'expression string
            ->where('a.texte LIKE :string')
            ->orWhere('a.lieu LIKE :string')
            ->orWhere('a.titre LIKE :string')
            // et où l'article est publié
            ->andWhere('a.statut = 1')
            // On concatène "%" avant et après l'expression recherchée 
            //pour chercher tous les enregistrements utilisant cette expression  
            ->setParameter('string', '%' . $request->get('search') . '%');
        // Si la requête a un champ "themes"
        if ($request->get('themes')) {
            // Pour chaque champ du tableau en question
            foreach (($request->get('themes')) as $value) {
                // l'article devra avoir pour thème la valeur du champ
                $qb->andWhere('a.theme = (:value)')
                    // On inclut la variable paramétrée dans la requête
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
            foreach (($request->get('tag')) as $value) {
                // la valeur doit faire partie de la propriété (collection) hashtags de l'article
                $qb->andWhere(":value MEMBER OF a.hashtags")
                    ->setParameter("value", $value);
            }
        }

        return $qb
            ->orderBy('a.id', 'DESC')
            // On construit la query d'après les spécifications indiquées
            ->getQuery()
            // On exécute la query
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByLikes($user)
    {
        return $this->createQueryBuilder('a')
            ->where(':user = a.auteurArticle')
            ->innerJoin('a.likes', 'l')
            ->setParameter('user', $user)
            ->groupBy('l.post')
            ->orderBy('COUNT(l.post)', 'DESC')
            ->setMaxResults(3)
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
