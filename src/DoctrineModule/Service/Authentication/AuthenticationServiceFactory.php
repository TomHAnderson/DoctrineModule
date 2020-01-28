<?php

namespace DoctrineModule\Service\Authentication;

use DoctrineModule\Service\AbstractFactory;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create authentication service object.
 */
class AuthenticationServiceFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthenticationService(
            $container->get('doctrine.authenticationstorage.' . $this->getName()),
            $container->get('doctrine.authenticationadapter.' . $this->getName())
        );
    }

    /**
     *
     * @param ServiceLocatorInterface $container
     * @return AuthenticationService
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, AuthenticationService::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        throw new \BadMethodCallException('Not implemented');
    }
}
