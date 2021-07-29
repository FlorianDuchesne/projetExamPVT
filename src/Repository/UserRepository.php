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
    public function SharedTheme($theme)
    {

        $query = $this->createQueryBuilder('u');
        $query->select('u, COUNT(t) AS mycount')
            ->innerJoin('u.publications', 'p')
            ->innerJoin('p.theme', 't')
            ->where('t = :theme')
            ->setParameter('theme', $theme)
            ->groupBy('u')
            ->orderBy('mycount', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // $entityManager = $this->getEntityManager();

        // $query = $entityManager->createQuery(
        //     'SELECT u 
        //     FROM App\Entity\User u
        //     INNER JOIN u.publications p
        //     INNER JOIN p.theme t
        //     WHERE p.theme = theme 
        //     GROUP BY u 
        //     ORDER BY COUNT(p.theme) DESC'
        // )->setParameter('theme', $theme)
        // ->setMaxResults(5)
        // ;

        // returns an array of Product objects
        // return $query->getResult();
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
