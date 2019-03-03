<?php


namespace App\EventSubscriber;

use App\Events\BlogPostEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BlogPostSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            BlogPostEvent::BLOG_POST_ADDED => 'onBlogPostAdded',
            BlogPostEvent::BLOG_POST_DELETED => 'onBlogPostDeleted',
            BlogPostEvent::BLOG_POST_UPDATED => 'onBlogPostUpdated',
        ];
    }

    public function onBlogPostAdded(BlogPostEvent $event): void
    {
        $this->logEntity(BlogPostEvent::BLOG_POST_ADDED, $event->getEntity());
    }
    public function onBlogPostDeleted(BlogPostEvent $event): void
    {
        $this->logEntity(BlogPostEvent::BLOG_POST_UPDATED, $event->getEntity());
    }
    public function onBlogPostUpdated(BlogPostEvent $event): void
    {
        $this->logEntity(BlogPostEvent::BLOG_POST_DELETED, $event->getEntity());
    }

}
