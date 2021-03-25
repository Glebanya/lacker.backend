<?php


namespace App\GraphQL\Resolver;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Symfony\Component\Security\Core\Security;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;


abstract class AbstractResolver implements ResolverInterface
{
    public function __construct(
        protected EntityManagerInterface $manager,
        protected Security $security
    )
    {}

    public abstract function resolve(int $id);

    /**
     * @return array
     */
    protected function getMethodsName() : array
    {
        try {
            return array_map(function (ReflectionMethod $method){
                return $method->getName();
            },(new ReflectionClass(get_called_class()))->getMethods()
            );
        }
        catch (Throwable) {
            return [];
        }
    }


    public function __invoke(ResolveInfo $info, $value, Argument $args)
    {
        if (in_array($method = $info->fieldName, $this->getMethodsName()))
        {
            return $this->$method($value, $args);
        }

    }
}