<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class UserToUsernameTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (user) to a string (username).
     *
     * @param User|null $user
     *
     * @return string
     */
    public function transform($user)
    {
        if (null === $user) {
            return '';
        }

        return $user->getUsername();
    }

    /**
     * Transforms a string (username) to an object (user).
     *
     * @param string $username
     *
     * @return User|null
     */
    public function reverseTransform($username)
    {
        return $this->manager->getRepository(User::class)->findOneBy(['username' => $username]);
    }
}
