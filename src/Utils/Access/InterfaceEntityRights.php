<?php


namespace App\Utils\Access;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface InterfaceEntityRights
{
    public const VIEW = 'View';
    public const EDIT = 'Edit';
    public const DELETE = 'Delete';
    public const ADD = 'Add';

    public function __construct(
        string $attribute,
        $object,
        TokenInterface $token,
        AccessVoter $accessVoter
    );

    public function checkAccess() : bool;

    public static function getAttributes() : array;

}