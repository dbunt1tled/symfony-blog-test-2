<?php


namespace App\Security;


use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class UserConfirmationService
{
    /** @var UserRepository */
    private $userRepository;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        UserRepository $userRepository,
        LoggerInterface $logger
    )
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function confirmUser(string $confirmationToken)
    {
        $this->logger->debug('Fetching user by confirmation token');
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$user) {
            $this->logger->debug('User by confirmation token not found');
            throw new InvalidConfirmationTokenException();
        }
        $user->setEnabled(true)
            ->setConfirmationToken(null);
        $this->userRepository->save($user);
        $this->logger->debug('User confirmed successfully');
    }
}
