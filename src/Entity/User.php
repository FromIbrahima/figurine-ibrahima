<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse e-mail.')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    /** Image de profil attribuée par défaut à l'inscription. */
    public const DEFAULT_IMAGE = 'default-avatar.png';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'adresse e-mail est obligatoire.')]
    #[Assert\Email(message: 'L\'adresse e-mail "{{ value }}" n\'est pas valide.')]
    private ?string $email = null;

    /**
     * @var list<string> Les rôles de l'utilisateur
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null Le mot de passe haché
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $lastname = null;

    #[Vich\UploadableField(mapping: 'users', fileNameProperty: 'imageName')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        maxSizeMessage: 'L\'image ne doit pas dépasser {{ limit }} {{ suffix }}.',
        mimeTypesMessage: 'Formats acceptés : JPG, PNG, WEBP ou GIF.',
    )]
    private ?File $imageFile = null;

    #[ORM\Column(length: 500)]
    private ?string $imageName = self::DEFAULT_IMAGE;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Figurine>
     */
    #[ORM\OneToMany(targetEntity: Figurine::class, mappedBy: 'owner', orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $figurines;

    public function __construct()
    {
        $this->figurines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Identifiant visuel de l'utilisateur (ici l'adresse e-mail).
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Chaque utilisateur possède au minimum le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullName(): string
    {
        return trim(sprintf('%s %s', $this->firstname, $this->lastname));
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

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
        // VichUploader remet le nom à null si l'image est supprimée : on revient à l'avatar par défaut
        $this->imageName = $imageName ?? self::DEFAULT_IMAGE;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Figurine>
     */
    public function getFigurines(): Collection
    {
        return $this->figurines;
    }

    public function addFigurine(Figurine $figurine): static
    {
        if (!$this->figurines->contains($figurine)) {
            $this->figurines->add($figurine);
            $figurine->setOwner($this);
        }

        return $this;
    }

    public function removeFigurine(Figurine $figurine): static
    {
        if ($this->figurines->removeElement($figurine)) {
            if ($figurine->getOwner() === $this) {
                $figurine->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * Le fichier image (objet File) ne doit pas être stocké en session :
     * on exclut aussi la collection de figurines.
     */
    public function __serialize(): array
    {
        $data = get_object_vars($this);
        unset($data['imageFile'], $data['figurines']);

        return $data;
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        $this->figurines = new ArrayCollection();
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // Aucune donnée sensible temporaire n'est stockée sur l'utilisateur.
    }
}
