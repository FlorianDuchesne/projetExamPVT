<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesReceived")
     * @ORM\JoinColumn(nullable=false)
     */
    private $received;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesSend")
     * @ORM\JoinColumn(nullable=false)
     */
    private $send;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $DateTime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $newMessage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getReceived(): ?User
    {
        return $this->received;
    }

    public function setReceived(?User $received): self
    {
        $this->received = $received;

        return $this;
    }

    public function getSend(): ?User
    {
        return $this->send;
    }

    public function setSend(?User $send): self
    {
        $this->send = $send;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->DateTime;
    }

    public function setDateTime(\DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

        return $this;
    }

    public function getNewMessage(): ?bool
    {
        return $this->newMessage;
    }

    public function setNewMessage(bool $newMessage): self
    {
        $this->newMessage = $newMessage;

        return $this;
    }
}
