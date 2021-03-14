<?php


namespace App\Utils\Login;

use Google_Client;
use App\Entity\Staff;
use App\Entity\User;
use App\Utils\Exception\Oauth\LoginException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GoogleUserLoginObject implements InterfaceLoginObject
{
    private array $data = [];

    public function __construct(
        private Google_Client $googleApiObject,
        private EntityManagerInterface $manager,
        private UserPasswordEncoderInterface $encoder
    ){}

    /**
     * @param array $params
     * @return InterfaceLoginObject
     * @throws LoginException
     */
    public function setData(array $params): InterfaceLoginObject
    {
        if (array_key_exists('client_id',$params) && $googleClient = $params['client_id'])
        {
            $params = $this->googleApiObject->verifyIdToken($googleClient);
            if ($params && is_array($params) && $this->data = $params)
            {
                return $this;
            }
        }
        throw new LoginException();
    }

    public function findUser(): UserInterface|User|Staff|null
    {
        if (array_key_exists('sub',$this->data))
        {
            if (!$user = $this->manager->getRepository(User::class)->findByGoogleClient($this->data['sub']))
            {
                if (!$user = $this->manager->getRepository(User::class)->findByMail($this->data['email']))
                {
                    return null;
                }
                $user->setGoogleId($this->data['sub']);
                $this->manager->flush();
            }
            return $user;

        }
        return null;
    }

    public function createUser(): UserInterface|User|Staff|null
    {
        $this->manager->persist(($user = new User())
            ->setEmail($this->data['email'])
            ->setUsername($this->data['name'])
            ->setPassword($this->encoder->encodePassword($user,md5(random_bytes(32),true)))
            ->setGoogleId($this->data['sub'])
        );
        $this->manager->flush();
        return $user;
    }
}