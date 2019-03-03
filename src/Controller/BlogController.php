<?php


declare(strict_types=1);

namespace App\Controller;


use App\Entity\BlogPost;
use App\Events\BlogPostEvent;
use App\Service\BlogPostService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
final class BlogController extends AbstractController
{
    /** @var BlogPostService */
    private $blogPostService;
    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, BlogPostService $blogPostService)
    {

        $this->blogPostService = $blogPostService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/{page}", name="blog_list", requirements={"page"="\d+"}, defaults={"page":5}, methods={"GET"})
     * @param int $page
     * @param Request $request
     * @return JsonResponse
     */
    public function list(int $page, Request $request): JsonResponse
    {
        $limit = (int)$request->get('limit', 10);
        $posts = $this->blogPostService->findAll();
        $posts = array_map(function (BlogPost $post) {
            return $this->generateUrl('blog_by_slug', ['slug' => $post->getSlug()]);
        }, $posts);
        return $this->json([
            'posts' => $posts,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @param BlogPost $post
     * @ParamConverter("post", class="App:BlogPost")
     * @return JsonResponse
     */
    public function post(BlogPost $post): JsonResponse
    {
        // $post = $this->blogPostService->find($id);
        return $this->json($post);
    }

    /**
     * @Route("/post/{id}", name="delete_post_id", requirements={"id"="\d+"}, methods={"DELETE"})
     * @param BlogPost $post
     * @ParamConverter("post", class="App:BlogPost")
     * @return JsonResponse
     */
    public function delete(BlogPost $post): JsonResponse
    {
        $this->blogPostService->remove($post);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug":"slug"}})
     * @param BlogPost $post
     * @return JsonResponse
     */
    public function postBySlug(BlogPost $post): JsonResponse
    {
        // $post = $this->blogPostService->findOneBy(['slug'=>$slug]);
        return $this->json($post);
    }

    /**
     * @Route("/add", name="post_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $blogPost = $this->blogPostService->newByRequest($request);
        //$this->blogPostService->save($blogPost);

        $this->dispatcher->dispatch(BlogPostEvent::BLOG_POST_ADDED, new BlogPostEvent($blogPost));
        return $this->json($blogPost);
    }
}
