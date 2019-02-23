<?php


namespace App\Controller;


use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageAction
{
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var ImageRepository */
    private $imageRepository;
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(
        ValidatorInterface $validator,
        FormFactoryInterface $formFactory,
        ImageRepository $imageRepository
    )
    {
        $this->formFactory = $formFactory;
        $this->imageRepository = $imageRepository;
        $this->validator = $validator;
    }

    public function __invoke(Request $request)
    {
        $image = new Image();
        $form = $this->formFactory->create(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->save($image);
            return $image;
        }
        throw new ValidationException($this->validator->validate($image));

    }
}
