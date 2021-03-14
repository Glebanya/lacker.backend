<?php


namespace App\Utils\Access\EntitiesRights;


use App\Entity\Staff;
use App\Utils\Access\InterfaceEntityRights;

class StaffRights extends EntityRightsBase implements InterfaceEntityRights
{
    public function checkAccess(): bool
    {
        if (($user = $this->token->getUser()) && $user instanceof Staff)
        {
            if ($this->object instanceof Staff)
            {
                return match ($this->attribute){
                    static::ADD, static::DELETE => $this->accessVoter->security->isGranted('ROLE_MANAGER'),
                    static::EDIT => $this->object->isEqualTo($user) || $this->accessVoter->security->isGranted('ROLE_MANAGER'),
                    static::VIEW => $this->accessVoter->security->isGranted('ROLE_STUFF'),
                    default => false
                };

            }
        }
        return false;
    }
}