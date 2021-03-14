<?php


namespace App\Utils\Access;



use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessVoter extends Voter
{

    public function __construct(
        public EntityManagerInterface $manager,
        public Security $security
    )
    {}

    protected function supports(string $attribute, $subject): bool
    {
        if (isset($subject))
        {
            if (array_key_exists($key = get_class($subject),$types = EntityRightsFactory::getSupportedTypes()))
            {
                return in_array($attribute,$types[$key]::getAttributes());
            }
        }
        return false;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN'))
        {
           return true;
        }
        return EntityRightsFactory::getObject($attribute,$subject,$token,$this)->checkAccess();
    }
}