<?php


namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }
        return $data instanceof User;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->isUserHimself($object)) {
            $context['groups'][] = 'get-owner';
        }
        return $this->passOn($object, $format, $context);
    }

    private function isUserHimself($object)
    {
        return $this->tokenStorage->getToken()->getUser() instanceof User && $object->getUsername() === $this->tokenStorage->getToken()->getUser()->getUsername();
    }
    private function passOn($object, $format, $context)
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException(sprintf('Cannot normalize object "%s" become the injecting serializer is not normalizer', $object));
        }
        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;
        return $this->serializer->normalize($object, $format, $context);
    }
}