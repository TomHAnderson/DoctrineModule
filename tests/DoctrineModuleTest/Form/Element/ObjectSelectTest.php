<?php

declare(strict_types=1);

namespace DoctrineModuleTest\Form\Element;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Form\Element\ObjectSelect;

use function get_class;

/**
 * Tests for the ObjectSelect element
 *
 * @link    http://www.doctrine-project.org/
 *
 * @covers  \DoctrineModule\Form\Element\ObjectSelect
 */
class ObjectSelectTest extends ProxyAwareElementTestCase
{
    protected ArrayCollection $values;

    protected ObjectSelect $element;

    /**
     * {@inheritDoc}.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->element = new ObjectSelect();

        $this->prepareProxy();
    }

    public function testSetValueWithCollection(): void
    {
        $this->element->setAttribute('multiple', true);

        $this->element->setValue(
            $this->values
        );

        $this->assertEquals(
            [1, 2],
            $this->element->getValue()
        );
    }

    public function testSetValueWithArray(): void
    {
        $this->element->setAttribute('multiple', true);

        $this->element->setValue(
            $this->values->toArray()
        );

        $this->assertEquals(
            [1, 2],
            $this->element->getValue()
        );
    }

    public function testSetValueSingleValue(): void
    {
        $value = $this->values->toArray();

        $this->element->setValue(
            $value[0]
        );

        $this->assertEquals(
            1,
            $this->element->getValue()
        );
    }

    public function testGetValueOptionsDoesntCauseInfiniteLoopIfProxyReturnsEmptyArrayAndValidatorIsInitialized(): void
    {
        $element = $this->createPartialMock(get_class($this->element), ['setValueOptions']);

        $options = [];

        $proxy = $this->createMock('DoctrineModule\Form\Element\Proxy');
        $proxy->expects($this->exactly(2))
              ->method('getValueOptions')
              ->will($this->returnValue($options));

        $element->expects($this->never())
                ->method('setValueOptions');

        $this->setProxyViaReflection($proxy, $element);
        $element->getInputSpecification();
        $this->assertEquals($options, $element->getValueOptions());
    }

    public function testGetValueOptionsDoesntInvokeProxyIfOptionsNotEmpty(): void
    {
        $options = ['foo' => 'bar'];

        $proxy = $this->createMock('DoctrineModule\Form\Element\Proxy');
        $proxy->expects($this->once())
              ->method('getValueOptions')
              ->will($this->returnValue($options));

        $this->setProxyViaReflection($proxy);

        $this->assertEquals($options, $this->element->getValueOptions());
        $this->assertEquals($options, $this->element->getValueOptions());
    }

    public function testOptionsCanBeSetSingle(): void
    {
        $proxy = $this->createMock('DoctrineModule\Form\Element\Proxy');
        $proxy->expects($this->once())->method('setOptions')->with(['is_method' => true]);

        $this->setProxyViaReflection($proxy);

        $this->element->setOption('is_method', true);
    }
}
