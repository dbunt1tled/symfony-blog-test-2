<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get" = {
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "put" = {
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "denormalization_context"={
 *                  "groups"={"put"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "put-reset-password" = {
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "method"="PUT",
 *              "path"="/users/{id}/reset-password",
 *              "controller" = ResetPasswordAction::class,
 *              "denormalization_context"={
 *                  "groups"={"put-reset-password"}
 *              }
 *          }
 *      },
 *     collectionOperations={
 *          "post" = {
 *              "denormalization_context"={
 *                  "groups"={"post"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          }
 *      },
 *
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_MODERATOR = 'ROLE_MODERATOR';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Passord not be empty",
     *     groups={"post","put"}
     * )
     * @Groups({"post", "put", "get-admin", "get"})
     */
    private $name;

    /**
     * @Assert\NotBlank(groups={"post"})
     * @ORM\Column(type="string", length=255)
     * @Groups({"post"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Password not be empty",
     *     groups={"post"}
     * )
     * @Assert\Length(
     *     min=6,
     *     max=255,
     *     minMessage="Min 6 symbols",
     *     groups={"post"}
     * )
     * @Groups({"post"})
     */
    private $plainPassword;

    /**
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
     *     "this.getPlainPassword() === this.getRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"post"}
     * )
     * @Groups({"post"})
     */
    private $retypedPassword;

    /**
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Groups({"put-reset-password"})
     */
    private $newPassword;
    /**
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Assert\Expression(
     *     "this.getNewPassword() == this.getNewRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"put-reset-password"}
     * )
     * @Groups({"put-reset-password"})
     */
    private $newRetypedPassword;

    /**
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Groups({"put-reset-password"})
     * @UserPassword(groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=70, unique=true)
     *
     * @Assert\NotBlank(
     *     message="Email not be empty",
     *     groups={"post","put"}
     * )
     * @Assert\Email(
     *     message="Email has wrong format",
     *     groups={"post","put"}
     * )
     * @Groups({"put", "post", "get-admin", "get-owner"})
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     * @Groups({"get-admin", "get-owner"})
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get"})
     */
    private $blogPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get"})
     */
    private $comments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeData;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $confirmationToken;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = [self::ROLE_USER];
        $this->enabled = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->contains($blogPost)) {
            $this->blogPosts->removeElement($blogPost);
            // set the owning side to null (unless already changed)
            if ($blogPost->getAuthor() === $this) {
                $blogPost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getSalt(): ?string
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function setRoles(array $role): self
    {
        $this->roles = $role;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = self::ROLE_USER;
        }
        return array_unique($roles);
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->id,
            $this->email,
            $this->password) = unserialize($serialized);
    }
    public function eraseCredentials()
    {
    }

    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword(string $retypedPassword): self
    {
        $this->retypedPassword = $retypedPassword;
        return $this;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;
        return $this;
    }
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    /**
     * @param mixed $newRetypedPassword
     * @return User
     */
    public function setNewRetypedPassword($newRetypedPassword)
    {
        $this->newRetypedPassword = $newRetypedPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasswordChangeData(): ?int
    {
        return $this->passwordChangeData;
    }

    /**
     * @param int $passwordChangeData
     * @return User
     */
    public function setPasswordChangeData(int $passwordChangeData): self
    {
        $this->passwordChangeData = $passwordChangeData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return User
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }



}
