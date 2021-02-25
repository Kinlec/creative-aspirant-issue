<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 * @ORM\Table(name="movie", indexes={@Index(columns={"title"})})
 */
final class Movie
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column()
     */
    private ?string $title;

    /**
     * @ORM\Column()
     */
    private ?string $link;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $description;

    /**
     * @ORM\Column(type="datetime", name="pub_date")
     */
    private ?\DateTime $pubDate;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $image;

    private ?ArrayCollection $likes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="likes")
     */
    private ?PersistentCollection $users;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPubDate(): ?\DateTime
    {
        return $this->pubDate;
    }

    public function setPubDate(?\DateTime $pubDate): self
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addLike($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeLike($this);
        }

        return $this;
    }
}
