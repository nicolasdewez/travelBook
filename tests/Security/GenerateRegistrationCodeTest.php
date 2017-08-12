<?php

namespace App\Tests\Security;

use App\Security\GenerateRegistrationCode;
use App\Security\RegistrationCode;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GenerateRegistrationCodeTest extends TestCase
{
    public function testExecute()
    {
        $validity = (new \DateTime())->add(new \DateInterval(sprintf('PT%dH', RegistrationCode::VALIDITY)));
        $expected = base64_encode(sprintf('ndewez-%d-%s', $validity->getTimestamp(), md5('ndewez.secret')));

        $generateRegistrationCode = new GenerateRegistrationCode(new NullLogger(),'secret');
        $this->assertSame($expected, $generateRegistrationCode->execute('ndewez'));
    }
}
