<?php


namespace App\Utils\Access;

use App\Entity;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use App\Utils\Access\EntitiesRights;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class EntityRightsFactory
{
    #[ArrayShape([Entity\Staff::class => "string", Entity\User::class => "string"])]
    public static function getSupportedTypes(): array
    {
        return [
            Entity\Staff::class => EntitiesRights\StaffRights::class,
             Entity\User::class => EntitiesRights\UserRights::class
        ];
    }

    #[Pure]
    public static function getObject(
        string $attribute,
        $object,
        TokenInterface $token,
        AccessVoter $accessVoter
    ) : InterfaceEntityRights
    {
        if (array_key_exists($key=get_class($object),$types = static::getSupportedTypes()))
        {
            return new $types[$key](
                $attribute,
                $object,
                $token,
                $accessVoter
            );
        }
    }
}