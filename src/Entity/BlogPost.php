<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "id":"exact",
 *          "title":"partial",
 *          "content":"partial",
 *          "author":"exact",
 *          "author.name":"partial"
 *     }
 * )
 * @ApiFilter(
 *      DateFilter::class,
 *      properties={
 *          "published"
 *     }
 * )
 * @ApiFilter(RangeFilter::class, properties={"id"})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id",
 *          "published",
 *          "title"
 *     },
 *     arguments={"orderParameterName":"_order"}
 * )
 * @ApiFilter(
 *     PropertyFilter::class,
 *     arguments={
 *          "parameterName":"properties",
 *          "overrideDefaultProperties": false,
 *          "whitelist": {"id", "author", "slug", "title", "content", "published"}
 *      }
 * )
 * @ApiResource(
 *     attributes={"order"={"published": "DESC"}, "maximumItemsPerPage"=10, "paginationPartial"=true},
 *     itemOperations={
 *      "get" = {
 *         "normalization_context"={
 *                  "groups"={"get-blog-post-with-author"}
 *              }
 *       },
 *      "put" = {
 *              "access_control"="is_granted('ROLE_MODERATOR') or is_granted('ROLE_USER') and object.getAuthor() == user"
 *          }
 *     },
 *     collectionOperations={
 *      "get",
 *      "post" = {
 *              "access_control"="is_granted('ROLE_USER')"
 *          }
 *      },
 *     denormalizationContext={
 *          "group" = {"post"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @UniqueEntity("slug")
 * @ORM\HasLifecycleCallbacks
 */
class BlogPost implements AuthoredEntityInterface, PublishedDateEntityInterface, AggregateRoot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get-blog-post-with-author"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"post", "get-blog-post-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Groups({"post", "get-blog-post-with-author"})
     *
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get-blog-post-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogPosts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-post-with-author"})
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogPost")
     * @ORM\JoinColumn(nullable=false)
     * @ApiSubresource()
     * @Groups({"get-blog-post-with-author"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * @ORM\JoinTable()
     * @ApiSubresource()
     * @Groups({"post","get-blog-post-with-author"})
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
            $comment->setBlogPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getBlogPost() === $this) {
                $comment->setBlogPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }
        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }
        return $this;
    }
    public function __toString(): string
    {
        return (string)$this->getTitle();
    }

    public function __toArray(): array
    {
        return get_object_vars($this);
    }
}
