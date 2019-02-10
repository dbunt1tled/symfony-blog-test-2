<?php


namespace App\Service;


use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class BlogPostService
{
    /** @var Serializer $serializer */
    private $serializer;
    /** @var BlogPostRepository */
    private $blogPostRepository;

    public function __construct(SerializerInterface $serializer, BlogPostRepository $blogPostRepository)
    {
        $this->serializer = $serializer;
        $this->blogPostRepository = $blogPostRepository;
    }

    public function save(BlogPost $blogPost): bool
    {
        return $this->blogPostRepository->save($blogPost);
    }
    public function remove(BlogPost $blogPost): bool
    {
        return $this->blogPostRepository->remove($blogPost);
    }
    public function newByRequest(Request $request): BlogPost
    {
        return $this->serializer->deserialize($request->getContent(), BlogPost::class, 'json');
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->blogPostRepository->find($id, $lockMode, $lockVersion);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->blogPostRepository->findOneBy($criteria, $orderBy);
    }

    public function findAll()
    {
        return $this->blogPostRepository->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->blogPostRepository->findBy($criteria, $orderBy, $limit, $offset);
    }
}