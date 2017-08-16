<?php

namespace App\Entity;

use App\Security\Role;
use App\Validator\Group;
use App\Workflow\RegistrationDefinitionWorkflow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="users", indexes={
 *     @ORM\Index(name="users_username", columns={"username"}),
 *     @ORM\Index(name="users_registration_code", columns={"registration_code"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(fields={"username"}, groups={"registration"})
 */
class User implements AdvancedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({
     *     "event_registration",
     *     "event_change_password",
     *     "event_update_account",
     *     "event_enable_account",
     *     "event_disable_account",
     *     "event_password_lost"
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=30, unique=true)
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=4, max=30, groups={"registration"})
     * @Assert\Regex(pattern="/^[a-z0-9_]+$/i", groups={"registration"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(length=50)
     *
     * @Assert\NotBlank(groups={"my_account", "registration", "edit"})
     * @Assert\Length(min=2, max=50, groups={"my_account", "registration"})
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(length=50)
     *
     * @Assert\NotBlank(groups={"my_account", "registration", "edit"})
     * @Assert\Length(min=2, max=50, groups={"my_account", "registration"})
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank(groups={"my_account", "registration", "edit"})
     * @Assert\Length(min=6, max=255, groups={"my_account", "registration"})
     * @Assert\Email(groups={"my_account", "registration"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(length=3)
     *
     * @Assert\NotBlank(groups={"my_account", "registration"})
     * @Assert\Choice(callback={"App\Translation\Locale", "getLocales"}, strict=true, groups={"my_account", "registration"})
     */
    private $locale;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     *
     * @Assert\NotBlank(groups={"edit"})
     */
    private $roles;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $firstConnection;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     */
    private $registrationState;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $registrationCode;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Travel", mappedBy="user", orphanRemoval=true)
     */
    private $travels;

    /** @var Picture[] */
    private $pictures;

    /**
     * Used in form.
     *
     * @var string
     *
     * @SecurityAssert\UserPassword(groups={"change_password", "my_account"})
     */
    private $currentPassword;

    /**
     * Used in form.
     *
     * @var string
     *
     * @Assert\NotBlank(groups={"change_password"})
     * @Assert\NotEqualTo(value="password", groups={"change_password", "my_account"})
     * @Assert\Length(min=6, groups={"change_password", "my_account"})
     */
    private $newPassword;

    public function __construct()
    {
        $this->roles = [Role::USER];
        $this->firstConnection = true;
        $this->enabled = false;
        $this->registrationState = RegistrationDefinitionWorkflow::PLACE_CREATED;
        $this->travels = new ArrayCollection();
        $this->pictures = [];
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname(string $firstname): User
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname(string $lastname): User
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return User
     */
    public function setLocale(string $locale): User
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array
     */
    public function getTitleRoles(): array
    {
        return array_map(function (string $role) {
            return Role::getTitleByRole($role);
        }, $this->roles);
    }

    /**
     * @return bool
     */
    public function isFirstConnection(): bool
    {
        return $this->firstConnection;
    }

    /**
     * @param bool $firstConnection
     *
     * @return User
     */
    public function setFirstConnection(bool $firstConnection): User
    {
        $this->firstConnection = $firstConnection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnabled(bool $enabled): User
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegistrationState(): string
    {
        return $this->registrationState;
    }

    /**
     * @param string $registrationState
     *
     * @return User
     */
    public function setRegistrationState(string $registrationState): User
    {
        $this->registrationState = $registrationState;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleRegistrationState(): string
    {
        return RegistrationDefinitionWorkflow::getTitleByPlace($this->registrationState);
    }

    /**
     * @return string
     */
    public function getRegistrationCode(): ?string
    {
        return $this->registrationCode;
    }

    /**
     * @param string $registrationCode
     *
     * @return User
     */
    public function setRegistrationCode(string $registrationCode): User
    {
        $this->registrationCode = $registrationCode;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTravels(): Collection
    {
        return $this->travels;
    }

    /**
     * @param Collection $travels
     *
     * @return User
     */
    public function setTravels(Collection $travels): User
    {
        $this->travels = $travels;

        return $this;
    }

    /**
     * @return Picture[]
     */
    public function getPictures(): array
    {
        $this->pictures = [];
        foreach ($this->travels as $travel) {
            $this->pictures = array_merge($this->pictures, $travel->getPictures());
        }

        return $this->pictures;
    }

    /**
     * @return string
     */
    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     *
     * @return User
     */
    public function setCurrentPassword(string $currentPassword): User
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     *
     * @return User
     */
    public function setNewPassword(string $newPassword): User
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param User                      $payload
     *
     * @Assert\Callback(groups={"change_password", "my_account"})
     */
    public function currentAndNewPasswordAreDifferent(ExecutionContextInterface $context, $payload)
    {
        if (null === $this->currentPassword && null === $this->newPassword && Group::USER_MY_ACCOUNT === $context->getGroup()) {
            return;
        }

        if ($this->currentPassword === $this->newPassword) {
            $context
                ->buildViolation('password.current_new_not_different')
                ->addViolation()
            ;
        }
    }

    /**
     * @param ExecutionContextInterface $context
     * @param User                      $payload
     *
     * @Assert\Callback(groups={"my_account"})
     */
    public function currentAndNewPasswordAreEmptyOrNot(ExecutionContextInterface $context, $payload)
    {
        if (null === $this->currentPassword && null === $this->newPassword) {
            return;
        }

        if (null !== $this->currentPassword && null !== $this->newPassword) {
            return;
        }

        $context
            ->buildViolation('password.current_new_not_empty')
            ->addViolation()
        ;
    }
}
