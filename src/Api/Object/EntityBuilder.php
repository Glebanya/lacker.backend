<?php


namespace App\Api\Object;


use Doctrine\ORM\EntityManagerInterface;

class EntityBuilder
{
    private EntityManagerInterface $manager;

    private \ReflectionClass $reflectionObject;

    /**
     * EntityBuilder constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    /**
     * @param string $class
     * @return $this
     * @throws \Exception
     */
    public function setEntityClass(string $class) : self {
        if (class_exists($class) && $class instanceof IBuildableEntity){
            $this->reflectionObject = new \ReflectionClass($class);
            return $this;
        }
        throw new \Exception();
    }

    public function create() : object {
        if (isset($this->reflectionObject)) {

        }
    }

}