<?php


namespace App\Utils\Authentication;


use App\Utils\Security\JWTObject;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class StaffTokenAuthenticator extends TokenAuthenticator
{
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $jwtObject = new JWTObject($credentials);
        if ($jwtObject->getType() === "staff" && $id =  $jwtObject->getUserId())
        {
            return $userProvider->loadUserByUsername($id);
        }
        return null;
    }
}