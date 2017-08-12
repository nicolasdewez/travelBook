<?php

namespace App\Security;

use App\Entity\User;
use App\Logger\Log;
use App\Repository\UserRepository;
use App\Workflow\RegistrationWorkflow;
use Psr\Log\LoggerInterface;

class CheckRegistrationCode
{
    /** @var UserRepository */
    private $repository;

    /** @var RegistrationWorkflow */
    private $workflow;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $secret;

    /** @var User */
    private $user;

    /**
     * @param UserRepository       $repository
     * @param RegistrationWorkflow $workflow
     * @param LoggerInterface      $logger
     * @param string               $secret
     */
    public function __construct(UserRepository $repository, RegistrationWorkflow $workflow, LoggerInterface $logger, string $secret)
    {
        $this->repository = $repository;
        $this->workflow = $workflow;
        $this->logger = $logger;
        $this->secret = $secret;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function execute(string $code): bool
    {
        $this->logger->info(sprintf('[%s] Check registration code %s', Log::SUBJECT_ACTIVE, $code));

        $now = new \DateTime();

        /** @var User $user */
        $user = $this->repository->findOneBy(['registrationCode' => $code]);
        if (null === $user) {
            $this->logger->error(sprintf('[%s] No user found with registration code %s', Log::SUBJECT_ACTIVE, $code));

            return false;
        }

        $code = base64_decode($code);
        list($codeUsername, $timestamp, $codeMd5) = explode('-', $code);

        // Username in database and code ko
        if ($codeUsername !== $user->getUsername()) {
            $this->logger->error(sprintf('[%s] Invalid registration code: user not valid (%s)', Log::SUBJECT_ACTIVE, $code));

            return false;
        }

        $md5 = md5(sprintf('%s.%s', $codeUsername, $this->secret));
        if ($md5 !== $codeMd5) {
            $this->logger->error(sprintf('[%s] Invalid registration code: md5 not valid (%s)', Log::SUBJECT_ACTIVE, $code));

            return false;
        }

        if ($now->getTimestamp() > $timestamp) {
            $this->logger->error(sprintf('[%s] Registration code expired (%s)', Log::SUBJECT_ACTIVE, $code));

            return false;
        }

        if (!$this->workflow->canApplyActive($user)) {
            $this->logger->error(sprintf('[%s] Registration is not in progress (%s)', Log::SUBJECT_ACTIVE, $code));

            return false;
        }

        $this->user = $user;

        return true;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
