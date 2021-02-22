<?php


namespace App\Api\Auth\Signup\Service;

use App\Api\Auth\Signup\Exception\SignUpException;
use \DateTime;
use App\Api\Auth\Signup\ISignUp;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class PureSignUpObject implements ISignUp
{
    private array $container = [];
    private EntityManagerInterface $manager;

    /**
     * PureSignUpObject constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
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
        if (array_key_exists('sex',$params)) {
            $this->container['sex'] = $params['sex'];
        }
        return $this;
    }

    public function findUser(): ?Client {
        if (array_key_exists('sub',$this->params)) {
            return $this->manager->getRepository(Client::class)->findByMail(
                $this->container['email']
            );
        }
        return null;
    }

    public function createUser(): Client
    {
        return (new Client())
            ->setPassword($this->container['password'])
            ->setMail($this->container['email'] )
            ->setPhone($this->container['phone'])
            ->setFullName($this->container['name'])
            ->setSex($this->container['sex'])
            ->setUpdateDate(new DateTime('NOW'))
            ->setCreateDate(new DateTime('NOW'));
    }
}