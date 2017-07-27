<?php

namespace App\Security;

use App\Logger\Log;
use Psr\Log\LoggerInterface;

class GenerateRegistrationCode
{
    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $secret;

    /**
     * @param LoggerInterface $logger
     * @param string          $secret
     */
    public function __construct(LoggerInterface $logger, string $secret)
    {
        $this->logger = $logger;
        $this->secret = $secret;
    }

    /**
     * @param string $username
     *
     * @return string
     */
    public function execute(string $username): string
    {
        $this->logger->info(sprintf(
            '[%s] Generate registration code for username: %s',
            Log::SUBJECT_REGISTRATION,
            $username
        ));

        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $md5 = md5(sprintf('%s.%s', $username, $this->secret));

        $registrationCode = sprintf('%s-%d-%s', $username, $validity->getTimestamp(), $md5);

        return base64_encode($registrationCode);
    }
}
