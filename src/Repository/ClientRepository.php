<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param array $fields
     * @return Client[] Returns an array of Client objects
     */
    public function findByUniqueParams(array $fields): array {
        $flag = true;
        $query = $this->createQueryBuilder('client');
        if(array_key_exists('email',$fields)) {
            $flag = false;
            $query->orWhere('client.mail = :email')->setParameter('email',$fields['email']);
        } if (array_key_exists('phone',$fields)) {
            $flag = false;
            $query->orWhere('client.phone = :phone')->setParameter('phone',$fields['phone']);
        } if ($flag){
            return [];
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param $mail
     * @return Client|null
     * @throws NonUniqueResultException
     */
    public function findByMail(string $mail): ?Client {
        return $this->createQueryBuilder('client')
            ->andWhere('client.mail = :email')
            ->setParameter('email',$mail)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $phone
     * @return Client|null
     * @throws NonUniqueResultException
     */
    public function findByPhone(string $phone): ?Client {
        return $this->createQueryBuilder('client')
            ->andWhere('client.phone = :phone')
            ->setParameter('phone',$phone)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $googleId
     * @param string $email
     * @return Client|null
     * @throws NonUniqueResultException
     */
    public function findByGoogleClient(string $googleId,string $email): ?Client {
        return $this->createQueryBuilder('client')
            ->orWhere('client.google_id = :google_id')
            ->orWhere('client.mail = :email')
            ->setParameter('email', $email)
            ->setParameter('google_id',$googleId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Client[] Returns an array of Client objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
