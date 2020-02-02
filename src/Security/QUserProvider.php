<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\QApiRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class QUserProvider implements UserProviderInterface
{
    /**
     * @var QApiRepository
     */
    private $apiRepository;

    public function __construct(QApiRepository $apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username)
    {
        throw new \LogicException('Unsupported operation');
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        // TODO: implement checking if user data is synced between session data and API
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class)
    {
        return User::class === $class;
    }
}
