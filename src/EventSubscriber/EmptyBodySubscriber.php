<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['handleEmptyBody', EventPriorities::POST_DESERIALIZE],
        ];
    }

    public function handleEmptyBody(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $event->getRequest()->getMethod();
        $route = $request->get('_route');

        if (
            mb_strpos($route, 'api') !== 0 ||
            !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT]) ||
            !in_array($request->getContentType(), ['html', 'form'])
            ) {
            return;
        }
        $data = $event->getRequest()->get('data');

        if (null === $data) {
            throw new EmptyBodyException();
        }
    }
}
