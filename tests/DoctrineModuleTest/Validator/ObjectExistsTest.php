<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineModuleTest\Validator\Adapter;

use stdClass;
use PHPUnit\Framework\TestCase as BaseTestCase;
use DoctrineModule\Validator\ObjectExists;

/**
 * Tests for the ObjectExists validator
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 *
 * @covers \DoctrineModule\Validator\ObjectExists
 */
class ObjectExistsTest extends BaseTestCase
{
    public function testCanValidateWithSingleField()
    {
        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');

        $repository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(['matchKey' => 'matchValue'])
            ->will($this->returnValue(new stdClass()));

        $validator = new ObjectExists(['object_repository' => $repository, 'fields' => 'matchKey']);

        $this->assertTrue($validator->isValid('matchValue'));
        $this->assertTrue($validator->isValid(['matchKey' => 'matchValue']));
    }

    public function testCanValidateWithMultipleFields()
    {
        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->with(['firstMatchKey' => 'firstMatchValue', 'secondMatchKey' => 'secondMatchValue'])
            ->will($this->returnValue(new stdClass()));

        $validator = new ObjectExists([
            'object_repository' => $repository,
            'fields'            => [
                'firstMatchKey',
                'secondMatchKey',
            ],
        ]);
        $this->assertTrue(
            $validator->isValid([
                'firstMatchKey'  => 'firstMatchValue',
                'secondMatchKey' => 'secondMatchValue',
            ])
        );
        $this->assertTrue($validator->isValid(['firstMatchValue', 'secondMatchValue']));
    }

    public function testCanValidateFalseOnNoResult()
    {
        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $validator = new ObjectExists([
            'object_repository' => $repository,
            'fields'            => 'field',
        ]);
        $this->assertFalse($validator->isValid('value'));
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testWillRefuseMissingRepository()
    {
        new ObjectExists(['fields' => 'field']);
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testWillRefuseNonObjectRepository()
    {

        new ObjectExists(['object_repository' => 'invalid', 'fields' => 'field']);
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testWillRefuseInvalidRepository()
    {

        new ObjectExists(['object_repository' => new stdClass(), 'fields' => 'field']);
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testWillRefuseMissingFields()
    {

        new ObjectExists([
            'object_repository' => $this->createMock('Doctrine\Common\Persistence\ObjectRepository'),
        ]);
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testWillRefuseEmptyFields()
    {

        new ObjectExists([
            'object_repository' => $this->createMock('Doctrine\Common\Persistence\ObjectRepository'),
            'fields'            => [],
        ]);
    }

    /**
     * @expectedException Zend\Validator\Exception\InvalidArgumentException
     */
    public function testWillRefuseNonStringFields()
    {
        new ObjectExists([
            'object_repository' => $this->createMock('Doctrine\Common\Persistence\ObjectRepository'),
            'fields'            => [123],
        ]);
    }

    /**
     * @expectedException Zend\Validator\Exception\RuntimeException
     * @expectedExceptionMessage Provided values count is 1, while expected number of fields to be matched is 2
     */
    public function testWillNotValidateOnFieldsCountMismatch()
    {
        $validator = new ObjectExists([
            'object_repository' => $this->createMock('Doctrine\Common\Persistence\ObjectRepository'),
            'fields'            => ['field1', 'field2'],
        ]);
        $validator->isValid(['field1Value']);
    }

    /**
     * @expectedException Zend\Validator\Exception\RuntimeException
     * @expectedExceptionMessage Field "field2" was not provided, but was expected since the configured field lists needs it for validation
     */
    public function testWillNotValidateOnFieldKeysMismatch()
    {        
        $validator = new ObjectExists([
            'object_repository' => $this->createMock('Doctrine\Common\Persistence\ObjectRepository'),
            'fields'            => ['field1', 'field2'],
        ]);

        $validator->isValid(['field1' => 'field1Value']);
    }

    public function testErrorMessageIsStringInsteadArray()
    {
        $repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $validator  = new ObjectExists([
            'object_repository' => $this->createMock('Doctrine\Common\Persistence\ObjectRepository'),
            'fields'            => 'field',
        ]);

        $this->assertFalse($validator->isValid('value'));

        $messageTemplates = $validator->getMessageTemplates();

        $expectedMessage = str_replace(
            '%value%',
            'value',
            $messageTemplates[ObjectExists::ERROR_NO_OBJECT_FOUND]
        );
        $messages        = $validator->getMessages();
        $receivedMessage = $messages[ObjectExists::ERROR_NO_OBJECT_FOUND];

        $this->assertTrue($expectedMessage == $receivedMessage);
    }
}
