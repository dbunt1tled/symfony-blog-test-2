<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordAction
{
    /** @var ValidatorInterface */
    private $validator;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var UserRepository */
    private $userRepository;
    /** @var JWTTokenManagerInterface */
    private $JWTTokenManager;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTTokenManager
    )
    {
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->JWTTokenManager = $JWTTokenManager;

    }

    public function __invoke(User $user)
    {
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse([
                'errors' => (string) $errors,
            ]);
        }
        $user->setPasswordChangeData(time());
        $user->setPlainPassword($user->getNewPassword());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        $this->userRepository->save($user);
        $token = $this->JWTTokenManager->create($user);

        return new JsonResponse([
         'token' => $token
        ]);
    }
}