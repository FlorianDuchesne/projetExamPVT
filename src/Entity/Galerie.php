<?php

namespace App\Entity;

use Serializable;
use App\Repository\GalerieRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GalerieRepository::class)
 * @Vich\Uploadable
 */
class Galerie implements Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="galerie", fileNameProperty="img")
     * @Assert\All({
     *      @Assert\Image(minWidth =700, minWidthMessage="la largeur de l'image doit être supérieur à 700 pixels", maxWidth = 1700, maxWidthMessage="la largeur de l'image ne doit pas dépasser 1700 px", mimeTypes="image/*", mimeTypesMessage="Le fichier doit être une image")
     * })
     * @var File|null
     */
    private $imageFile;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    *
    * @var \DateTime
    */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legende;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="galeries", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg( ?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getLegende(): ?string
    {
        return $this->legende;
    }

    public function setLegende(?string $legende): self
    {
        $this->legende = $legende;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
            $this->imageFile = $imageFile;
            if ($imageFile instanceof UploadedFile) {
                $this->updatedAt = new \DateTime();
            }    
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    
    /**
    * @return string
    */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt):self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->legende,
            $this->article,
            // see section on salt below
            // $this->salt,
        ));
    }
    
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->legende,
            $this->article,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->legende;
    }

}
