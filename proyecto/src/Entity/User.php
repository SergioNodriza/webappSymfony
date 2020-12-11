<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *     collectionOperations={"get"={"normalization_context"={"groups"="user:list"}}},
 *     itemOperations={"get"={"normalization_context"={"groups"="user:item"}}},
 *     paginationEnabled=false
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:list", "user:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:list", "user:item"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:list", "user:item"})
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="user", orphanRemoval=true)
     */
    private $items;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:list", "user:item"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255, options={"default": "submitted"})
     * @Groups({"user:list", "user:item"})
     */
    private $state = "registered";

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->roles[] = 'ROLE_USER';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItems(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setUser($this);
        }

        return $this;
    }

    public function removeItems(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getUser() === $this) {
                $item->setUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getRoles()
    {
        return array_unique($this->roles);
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->getName();
    }

    public function eraseCredentials()
    {
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
