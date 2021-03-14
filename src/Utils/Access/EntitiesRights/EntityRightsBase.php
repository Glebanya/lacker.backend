<?php


namespace App\Utils\Access\EntitiesRights;


use App\Utils\Access\AccessVoter;
use App\Utils\Access\InterfaceEntityRights;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class EntityRightsBase implements InterfaceEntityRights
{
    public const ENTITY = '';

    public function __construct(
        protected string $attribute,
        protected $object,
        protected TokenInterface $token,
        protected AccessVoter $accessVoter
    )
    {}

    public static function getAttributes(): array
    {
        return [
            static::DELETE,
            static::VIEW,
            static::EDIT,
            static::ADD
        ];
    }
}