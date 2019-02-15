<?php


namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/confirm-user/{token}", name="default_confirm_token")
     * @param string $token
     * @param UserConfirmationService $userConfirmationService
     * @return RedirectResponse
     */
    public function confirmUser(string $token, UserConfirmationService $userConfirmationService): RedirectResponse
    {
        $userConfirmationService->confirmUser($token);
        return $this->redirectToRoute('default_index');
    }
}