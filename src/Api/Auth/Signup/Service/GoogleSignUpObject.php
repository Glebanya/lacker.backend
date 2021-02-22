<?php


namespace App\Api\Auth\Signup\Service;

use App\Api\Auth\Signup\Exception\SignUpException;
use App\Api\Auth\Signup\ISignUp;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;

class GoogleSignUpObject implements ISignUp
{
    private EntityManagerInterface $manager;

    private array $params;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->manager = $entityManager;
    }

    /**
     * @param array $params
     * @return ISignUp
     * @throws SignUpException
     */
    public function setData(array $params): ISignUp {
        if (array_key_exists('client_id',$params) && $googleClient = $params['client_id']) {
            if ($this->params = (new Google_Client(['client_id' => $_ENV['APP_GOOGLE_CLIENT_ID']]))->verifyIdToken($googleClient)) {
                return $this;
            }
        }
        throw new SignUpException();
    }

    public function findUser(): ?Client {
        if (array_key_exists('sub',$this->params)) {
            return $this->manager->getRepository(Client::class)->findByGoogleClient(
                $this->params['sub'],
                $this->params['email']
            );
        }
        return null;
    }

    public function createUser(): Client {
        return (new Client())
            ->setMail($this->params['email'])
            ->setFullName($this->params['name']);
    }
}