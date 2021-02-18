<?php


namespace App\Api\Auth\Signup\Service;

use \DateTime;
use App\Api\Auth\Signup\ISignUp;
use App\Entity\Client;

class PureSignUpObject implements ISignUp
{

    private array $container = [];

    public function setData(array $params): ISignUp
    {
        $this->container['email'] = $params['email'];
        $this->container['sex'] = $params['sex'];
        $this->container['name'] = $params['name'] ?? 'user';
        $this->container['phone'] = $params['phone'];
        return $this;
    }

    public function getUserData(): Client
    {
        return (new Client())
            ->setMail($this->container['email'] )
            ->setPhone($this->container['phone'])
            ->setFullName($this->container['name'])
            ->setSex($this->container['sex'])
            ->setUpdateDate(new DateTime('NOW'))
            ->setCreateDate(new DateTime('NOW'));
    }
}