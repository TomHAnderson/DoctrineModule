<?php

declare(strict_types=1);

namespace DoctrineModule\Validator\Service;

use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\Service\Exception\ServiceCreationException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;
use function interface_exists;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-09-04 at 11:12:27.
 *
 * @coversDefaultClass DoctrineModule\Validator\Service\NoObjectExistsFactory
 * @group validator
 */
class NoObjectExistsFactoryTest extends TestCase
{
    /** @var NoObjectExistsFactory */
    private $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        $this->object = new NoObjectExistsFactory();
    }

    /**
     * @coversNothing
     */
    public function testCallable() : void
    {
        $this->assertIsCallable($this->object);
    }

    /**
     * @covers ::__invoke
     * @covers ::container
     * @covers ::getRepository
     * @covers ::getObjectManager
     * @covers ::getFields
     */
    public function testInvoke() : void
    {
        $options = [
            'target_class' => 'Foo\Bar',
            'fields'       => ['test'],
        ];

        $repository    = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldBeCalled()
            ->willReturn($objectManager->reveal());

        $instance = $this->object->__invoke(
            $container->reveal(),
            NoObjectExists::class,
            $options
        );
        $this->assertInstanceOf(NoObjectExists::class, $instance);
    }

    /**
     * @covers ::__invoke
     * @covers ::container
     * @covers ::getRepository
     * @covers ::getObjectManager
     * @covers ::getFields
     */
    public function testInvokeWithObjectManagerGiven() : void
    {
        $repository    = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $options = [
            'target_class'   => 'Foo\Bar',
            'object_manager' => $objectManager->reveal(),
            'fields'         => ['test'],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldNotBeCalled();

        $instance = $this->object->__invoke(
            $container->reveal(),
            NoObjectExists::class,
            $options
        );
        $this->assertInstanceOf(NoObjectExists::class, $instance);
    }

    /**
     * @covers ::merge
     */
    public function testInvokeWithMerge() : void
    {
        $options = [
            'target_class' => 'Foo\Bar',
            'fields'       => ['test'],
            'messages'     => [NoObjectExists::ERROR_OBJECT_FOUND => 'test'],
        ];

        $repository    = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldBeCalled()
            ->willReturn($objectManager->reveal());

        $instance  = $this->object->__invoke(
            $container->reveal(),
            NoObjectExists::class,
            $options
        );
        $templates = $instance->getMessageTemplates();
        $this->assertArrayHasKey(NoObjectExists::ERROR_OBJECT_FOUND, $templates);
        $this->assertSame('test', $templates[NoObjectExists::ERROR_OBJECT_FOUND]);
    }

    /**
     * @covers ::getRepository
     */
    public function testInvokeWithoutTargetClass() : void
    {
        $this->expectException(ServiceCreationException::class);

        $container = $this->prophesize(ContainerInterface::class);
        $this->object->__invoke(
            $container->reveal(),
            NoObjectExists::class,
            []
        );
    }

    /**
     * @covers ::createService
     * @covers ::setCreationOptions
     */
    public function testCreateService() : void
    {
        $options = [
            'target_class' => 'Foo\Bar',
            'fields'       => ['test'],
        ];

        $repository    = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldBeCalled()
            ->willReturn($objectManager->reveal());

        $this->object->setCreationOptions($options);
        $instance = $this->object->createService($container->reveal());
        $this->assertInstanceOf(NoObjectExists::class, $instance);
    }

    /**
     * @covers ::container
     */
    public function testCreateServiceWithServiceLocatorAwareInterface() : void
    {
        if (! interface_exists(ServiceLocatorAwareInterface::class)) {
            $this->markTestSkipped('ServiceLocatorAwareInterface not defined');
        }

        $options = [
            'target_class' => 'Foo\Bar',
            'fields'       => ['test'],
        ];

        $repository    = $this->prophesize(ObjectRepository::class);
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->getRepository('Foo\Bar')
            ->shouldBeCalled()
            ->willReturn($repository->reveal());

        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->get('doctrine.entitymanager.orm_default')
            ->shouldBeCalled()
            ->willReturn($objectManager->reveal());

        $parentContainer = $this->prophesize(ServiceLocatorInterface::class);
        $parentContainer->willImplement(ServiceLocatorAwareInterface::class);
        $parentContainer->getServiceLocator()
            ->shouldBeCalled()
            ->willReturn($container->reveal());

        $this->object->setCreationOptions($options);
        $instance = $this->object->createService($parentContainer->reveal());
        $this->assertInstanceOf(NoObjectExists::class, $instance);
    }
}
