<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de renseigner ce champ")
     * @Assert\Url()
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Merci de renseigner ce champ")
     * @Assert\Length(min="10",max="60",minMessage="Le titre de l'image doit faire au moinx 10 caractères.",maxMessage="Le titre de l'image ne peut exceder 60 caractères.")
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity=Annonce::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getAd(): ?Annonce
    {
        return $this->ad;
    }

    public function setAd(?Annonce $ad): self
    {
        $this->ad = $ad;

        return $this;
    }
}
