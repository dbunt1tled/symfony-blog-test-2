<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserConfirmation
 * @package App\Entity
 * @ApiResource(
 *     collectionOperations={
 *          "post" =  {
                "path" = "/users/confirm"
 *          }
 *     },
 *     itemOperations={}
 * )
 */
class UserConfirmation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="30", max="40")
     */
    public $confirmationToken;

}