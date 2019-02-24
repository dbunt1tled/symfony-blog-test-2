<?php


namespace App\Controller\Admin;

use App\Entity\User;
use App\Security\TokenGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends EasyAdminController
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var TokenGenerator */
    private $tokenGenerator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator)
    {

        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
        $this->encodeUserPassword($entity);
        parent::persistEntity($entity);
    }

    /**
     * @param User $entity
     */
    protected function updateEntity($entity)
    {
        $this->encodeUserPassword($entity);
        parent::updateEntity($entity);
    }

    /**
     * @param User $entity
     */
    private function encodeUserPassword(User $entity): void
    {
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword()));
    }

}
