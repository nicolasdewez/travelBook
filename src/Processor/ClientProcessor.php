<?php

namespace App\Processor;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ClientProcessor
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var RequestStack */
    private $requestStack;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack          $requestStack
     */
    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function processRecord(array $record): array
    {
        // Get Ip
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return $record;
        }

        $record['extra']['ips'] = $request->getClientIps();

        // Get username if user connected
        if (null === $token = $this->tokenStorage->getToken()) {
            return $record;
        }

        if (null === $token->getUser()) {
            return $record;
        }

        $record['extra']['username'] = $token->getUsername();

        return $record;
    }
}
