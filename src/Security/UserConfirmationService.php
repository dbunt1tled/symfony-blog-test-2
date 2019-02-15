<?php


namespace App\Security;


use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function confirmUser(string $confirmationToken)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$user) {
            throw new NotFoundHttpException();
        }
        $user->setEnabled(true)
            ->setConfirmationToken(null);
        $this->userRepository->save($user);
    }
}