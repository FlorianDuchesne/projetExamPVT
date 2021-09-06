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
    public function findByLikes($user)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.publications', 'a')
            ->innerJoin('a.likes', 'l')
            ->where('u != :user')
            ->setParameter('user', $user)
            ->groupBy('l.post')
            ->orderBy('COUNT(l.post)', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByMessages($user)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.messagesReceived', 'mr')
            ->leftJoin('u.messagesSend', 'ms')
            ->where(':user = ms.send')
            ->orWhere(':user = ms.received')
            ->orWhere(':user = mr.received')
            ->orWhere(':user = mr.send')
            ->setParameter('user', $user)
            // ->orderBy('u.pseudo', 'ASC')
            ->orderBy('ms.DateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findBySearch($string)
    {
        return $this->createQueryBuilder('u')
            ->where('u.pseudo LIKE :string')
            ->setParameter('string', '%' . $string . '%')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function SharedPaysAndTheme($pays, $theme, $user)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.publications', 'a')
            ->innerJoin('a.pays', 'p')
            ->innerJoin('a.theme', 't')
            ->where('t = :theme')
            ->andWhere('p = :pays')
            ->andWhere('u != :user')
            ->setParameter('theme', $theme)
            ->setParameter('pays', $pays)
            ->setParameter('user', $user)
            ->groupBy('a.auteurArticle')
            ->orderBy('COUNT(a.pays)', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function SharedPays($pays, $user)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.publications', 'a')
            ->innerJoin('a.pays', 'p')
            ->where('p = :pays')
            ->andWhere('u != :user')
            ->setParameter('pays', $pays)
            ->setParameter('user', $user)
            ->groupBy('a.auteurArticle')
            ->orderBy('COUNT(a.pays)', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function SharedTheme($theme, $user)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.publications', 'a')
            ->innerJoin('a.theme', 't')
            ->where('t = :theme')
            ->andWhere('u != :user')
            ->setParameter('theme', $theme)
            ->setParameter('user', $user)
            ->groupBy('a.auteurArticle')
            ->orderBy('COUNT(a.theme)', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
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
