<?php


namespace App\Api\Auth\Signup\Service;

use App\Api\Auth\Security\PasswordHandler;
use App\Api\Auth\Signup\Exception\SignUpException;
use App\Entity\Stuff;
use \DateTime;
use App\Api\Auth\Signup\ISignUp;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class StuffSignUpObject implements ISignUp
{
    private array $container;

    private EntityManagerInterface $manager;

    /**
     * StuffSignUpObject constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
        $this->container = [];
    }

    /**
     * @param array $params
     * @return ISignUp
     * @throws SignUpException
     */
    public function setData(array $params): ISignUp {

        if (array_key_exists('email',$params)) {
            $this->container['email'] = $params['email'];
        } else {
            throw new SignUpException();
        }
        if (array_key_exists('name',$params)) {
            $this->container['name'] = $params['name'];
        } else {
            throw new SignUpException();
        }
        if (array_key_exists('password',$params)) {
            $this->container['password'] = $params['password'];
        } else {
            throw new SignUpException();
        }
        if (array_key_exists('phone',$params)) {
            $this->container['phone'] = $params['phone'];
        } else {
            throw new SignUpException();
        }
        return $this;
    }

    /**
     * @return Client|null
     * @throws SignUpException
     */
    public function findUser(): ?Stuff {
        if ($user = $this->manager->getRepository(Stuff::class)->findByMail($this->container['email'])){
            if (PasswordHandler::validate($user,$this->container['password'])) {
                return $user;
            }

        }
        throw new SignUpException();
    }

    /**
     * @return Stuff
     */
    public function createUser(): Stuff
    {
        return (new Stuff())
            ->setMail($this->container['email'] )
            ->setTelephone($this->container['phone'])
            ->setName($this->container['name'])
            ->setPassword($this->container['password'])
            ->getBusiness($this->container['business'])
            ;
    }
}