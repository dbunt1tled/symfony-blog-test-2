<?php


namespace App\Tests\EventSubscriber;

use App\Entity\BlogPost;
use App\Entity\User;
use App\EventSubscriber\EntityAddAuthorSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use PHPUnit\Framework\MockObject\MockObject;

final class EntityAddAuthorSubscriberTest extends TestCase
{
    public function testConfiguration(): void
    {
        $result = EntityAddAuthorSubscriber::getSubscribedEvents();
        $this->arrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['addAuthor', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }

    /**
     * @dataProvider providerSetAuthorCall
     * @param string $className
     * @param int $countAuthorCalls
     * @param string $methodRequest
     * @param bool $hasToken
     */
    public function testSetAuthorCall(string $className, int $countAuthorCalls, string $methodRequest, bool $hasToken): void
    {
        $entityMock = $this->getEntityMock($className, $countAuthorCalls);
        $tokenStorageMock = $this->getTokenStorageMock($countAuthorCalls, $hasToken);
        $eventMock = $this->getEventMock($methodRequest, $entityMock);
        (new EntityAddAuthorSubscriber($tokenStorageMock))->addAuthor($eventMock);
    }

    public function testNoTokenPresent(): void
    {
        $tokenStorageMock = $this->getTokenStorageMock(0, false);
        $eventMock = $this->getEventMock('POST', new class {});
        (new EntityAddAuthorSubscriber($tokenStorageMock))->addAuthor($eventMock);
    }

    public function providerSetAuthorCall(): array
    {
        return [
            [BlogPost::class, 1, 'POST', true],
            [BlogPost::class, 0, 'GET', true],
            ['NotExists', 0, 'POST', true]
        ];
    }

    /**
     * @param int $count
     * @param bool $hasToken
     * @return MockObject|TokenStorageInterface
     */
    private function getTokenStorageMock(int $count = 0, bool $hasToken = true): MockObject
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();
        $tokenMock->expects($this->exactly($count))
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMockForAbstractClass();
        $tokenStorageMock->expects($this->exactly($count))
            ->method('getToken')
            ->willReturn($hasToken ? $tokenMock : null);
        return $tokenStorageMock;
    }

    /**
     * @param string $method
     * @param $controllerResult
     * @return MockObject|GetResponseForControllerResultEvent
     */
    private function getEventMock(string $method, $controllerResult )
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();

        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $eventMock = $this->getMockBuilder(GetResponseForControllerResultEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($controllerResult);

        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }

    /**
     * @param $class
     * @param int $count
     * @return MockObject
     */
    private function getEntityMock($class, int  $count = 0): MockObject
    {
        $entityMock = $this->getMockBuilder($class)
            ->setMethods(['setAuthor'])
            ->getMock();
        $assertCount = $this->never();
        if ($count > 0) {
            $assertCount = $this->exactly($count);
        }
        $entityMock->expects($assertCount)
            ->method('setAuthor');

        return $entityMock;
    }
}
