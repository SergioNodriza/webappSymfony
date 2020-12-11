<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 * @ApiResource(
 *     collectionOperations={"get"={"normalization_context"={"groups"="conference:list"}}},
 *     itemOperations={"get"={"normalization_context"={"groups"="conference:item"}}},
 *     paginationEnabled=false
 * )
 */
class Item
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"conference:list", "conference:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"conference:list", "conference:item"})
     */
    private $title;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"conference:list", "conference:item"})
     */
    private $done;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"conference:list", "conference:item"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="items", cascade={"persist"})
     * @Groups({"conference:list", "conference:item"})
     */
    private $user;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}
