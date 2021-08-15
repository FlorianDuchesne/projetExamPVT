<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function SharedPaysAndTheme($paysId, $themeId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT u.id FROM user u
            INNER JOIN article a ON u.id = a.auteur_article_id 
            INNER JOIN pays p ON p.id = a.pays_id 
            INNER JOIN theme t ON t.id = a.theme_id 
            WHERE t.id = :themeId 
            AND p.id = :paysId 
            GROUP BY a.auteur_article_id 
            ORDER BY COUNT(a.pays_id) DESC
            LIMIT 5
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['paysId' => $paysId, 'themeId' => $themeId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function SharedPays($paysId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT u.id FROM user u
            INNER JOIN article a ON u.id = a.auteur_article_id 
            INNER JOIN pays p ON p.id = a.pays_id 
            WHERE p.id = :paysId 
            GROUP BY a.auteur_article_id 
            ORDER BY COUNT(a.pays_id) DESC
            LIMIT 5
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['paysId' => $paysId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function SharedTheme($themeId)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT u.id FROM user u
            INNER JOIN article a ON u.id = a.auteur_article_id 
            INNER JOIN theme t ON t.id = a.theme_id 
            WHERE t.id = :themeId 
            GROUP BY a.auteur_article_id 
            ORDER BY COUNT(a.theme_id) DESC
            LIMIT 5
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['themeId' => $themeId]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAllAssociative();

        // $query = $this->createQueryBuilder('u');
        // $query->select('u, COUNT(p.theme) AS mycount')
        //     ->innerJoin('u.publications', 'p')
        //     ->innerJoin('p.theme', 't')
        //     ->where('t.id = :themeId')
        //     ->setParameter('themeId', $themeId)
        //     ->groupBy('p.auteurArticle')
        //     ->orderBy('mycount', 'DESC')
        //     ->setMaxResults(10)
        //     ->getQuery()
        //     ->getResult();

        // $entityManager = $this->getEntityManager();

        // $query = $entityManager->createQuery(
        //     'SELECT u 
        //     FROM App\Entity\User u
        //     INNER JOIN u.publications p
        //     INNER JOIN p.theme t
        //     WHERE t.id = :t.id 
        //     GROUP BY u 
        //     ORDER BY COUNT(p.theme) DESC'
        // )->setParameter('t.id', $themeId);

        // return $query->setMaxResults(5)->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
