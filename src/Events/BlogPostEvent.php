<?php


namespace App\Events;


class BlogPostEvent extends AbstractEvent
{
    public const BLOG_POST_ADDED = 'blog_post_added';
    public const BLOG_POST_UPDATED = 'blog_post_updated';
    public const BLOG_POST_DELETED = 'blog_post_deleted';
}
