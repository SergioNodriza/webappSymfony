<?php

namespace Mailer\Message;

class UserRegisterMessage
{
    private string $id;
    private string $name;
    private string $state;
    private ?string $info;

    public function __construct(string $id, string $name, string $state, ?string $info = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->state = $state;
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }
}