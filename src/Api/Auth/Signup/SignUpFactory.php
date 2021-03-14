<?php


namespace App\Api\Auth\Signup;


use Doctrine\ORM\EntityManagerInterface;

class SignUpFactory
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    public function create(string $type) : ISignUp
    {

        switch ($type)
        {
            case SignUpType::GOOGLE_AUTH_TYPE;
                return new Service\GoogleSignUpObject($this->manager);
            default;
                return new Service\StuffSignUpObject($this->manager);
        }
    }
}