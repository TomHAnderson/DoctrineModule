<?php


namespace DoctrineModule\Validator\Service;

use Interop\Container\ContainerInterface;
use DoctrineModule\Validator\ObjectExists;

/**
 * Factory for creating ObjectExists instances
 */
class ObjectExistsFactory extends AbstractValidatorFactory
{
    protected $validatorClass = ObjectExists::class;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = $this->container($container);

        $repository = $this->getRepository($container, $options);

        $validator = new ObjectExists($this->merge($options, [
            'object_repository' => $repository,
            'fields'            => $this->getFields($options),
        ]));

        return $validator;
    }
}
