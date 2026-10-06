<?php

namespace App\Entity;

use App\Repository\FigurineRepository;
use App\Traits\TimestampableTrait;
use App\Validator\ForbiddenWords;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: FigurineRepository::class)]
#[ORM\Table(name: 'figurines')]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Figurine
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.',
    )]
    #[ForbiddenWords(words: ['spam'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif.')]
    #[Assert\LessThan(value: 100000000, message: 'Le prix est trop élevé.')]
    private ?string $price = null;

    /**
     * Fichier image envoyé via le formulaire (non stocké en base, géré par VichUploader).
     */
    #[Vich\UploadableField(mapping: 'figurines', fileNameProperty: 'imageName')]
    #[Assert\Image(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        maxSizeMessage: 'L\'image ne doit pas dépasser {{ limit }} {{ suffix }}.',
        mimeTypesMessage: 'Formats acceptés : JPG, PNG, WEBP ou GIF.',
    )]
    private ?File $imageFile = null;

    #[ORM\Column(length: 500)]
    private ?string $imageName = null;

    #[ORM\ManyToOne(inversedBy: 'figurines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Lorsqu'une nouvelle image est envoyée, on modifie updatedAt pour que
     * Doctrine détecte un changement et que VichUploader enregistre le fichier.
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
