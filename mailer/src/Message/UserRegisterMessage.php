<?php

namespace Mailer\Message;

class UserRegisterMessage
{
    private string $id;
    private string $name;
    private string $state;

    public function __construct(string $id, string $name, string $state)
    {
        $this->id = $id;
        $this->name = $name;
        $this->state = $state;
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
}