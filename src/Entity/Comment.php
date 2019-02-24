<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={
 *          "order"={"published": "DESC"},
 *          "pagination_client_enabled"=true,
 *          "pagination_client_items_per_page"=true
 *     },
 *     itemOperations={
 *      "get",
 *      "put" = {
 *              "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user"
 *          }
 *     },
 *     collectionOperations={
 *      "get",
 *      "post" = {
 *              "access_control"="is_granted('ROLE_MODERATOR')"
 *          },
 *      "api_blog_posts_comments_get_subresource" = {
 *              "normalization_context"={
 *                  "groups"={"get-comment-with-author"}
 *              }
 *          }
 *      },
 *     denormalizationContext={
 *          "group" = {"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post", "get-comment-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get-comment-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-comment-with-author"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeImmutable
    {
        return $this->published;
    }

    public function setPublished(\DateTimeImmutable $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function __toString(): string
    {
        return mb_substr((string)$this->getContent(), 0 , 20) . '..';
    }
}
