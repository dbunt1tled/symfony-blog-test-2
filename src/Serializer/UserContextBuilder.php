<?php


namespace App\Serializer;

use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface */
    private $decorated;
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {

        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    final public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        // Class being to serialized / deserialized
        $resourceClass = $context['resource_class'] ?? null; // Default NULL
        if ($normalization === true && User::class === $resourceClass && isset($context['groups']) && $this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
            $context['groups'][] = 'get-admin';
        }
        return $context;
    }

}