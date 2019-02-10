<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 * @Route("/")
 */
final class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'action' => 'index',
            'time' => time(),
        ]);
    }
}