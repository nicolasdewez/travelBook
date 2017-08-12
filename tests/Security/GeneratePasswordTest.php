<?php

namespace App\Tests\Security;

use App\Security\GeneratePassword;
use PHPUnit\Framework\TestCase;

class GeneratePasswordTest extends TestCase
{
    public function testExecute()
    {
        $generatePassword = new GeneratePassword();
        $password = $generatePassword->execute();

        $specials = 0;
        $numbers = 0;
        $capital = 0;
        $tiny = 0;
        foreach (str_split($password) as $char) {
            $ascii = ord($char);
            if (GeneratePassword::SPECIALS[0] <= $ascii && $ascii <= GeneratePassword::SPECIALS[1]) {
                ++$specials;
                continue;
            }
            if (GeneratePassword::NUMBERS[0] <= $ascii && $ascii <= GeneratePassword::NUMBERS[1]) {
                ++$numbers;
                continue;
            }
            if (GeneratePassword::CAPITAL_LETTERS[0] <= $ascii && $ascii <= GeneratePassword::CAPITAL_LETTERS[1]) {
                ++$capital;
                continue;
            }
            if (GeneratePassword::TINY_LETTERS[0] <= $ascii && $ascii <= GeneratePassword::TINY_LETTERS[1]) {
                ++$tiny;
                continue;
            }
        }

        $this->assertGreaterThanOrEqual(1, $specials);
        $this->assertGreaterThanOrEqual(1, $numbers);
        $this->assertGreaterThanOrEqual(1, $capital);
        $this->assertGreaterThanOrEqual(1, $tiny);
    }

    /**
     * @dataProvider providerTestGetTypeChar
     *
     * @param array $expected
     * @param array $arguments
     */
    public function testGetTypeChar(array $expected, array $arguments)
    {
        $generatePassword = new GeneratePassword();

        $class = new \ReflectionClass($generatePassword);
        $method = $class->getMethod('getTypeChar');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invokeArgs($generatePassword, $arguments));
    }

    /**
     * @dataProvider providerTestGetChar
     *
     * @param string $expected
     * @param array  $arguments
     */
    public function testGetChar(string $expected, array $arguments)
    {
        $generatePassword = new GeneratePassword();

        $class = new \ReflectionClass($generatePassword);
        $method = $class->getMethod('getChar');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invokeArgs($generatePassword, $arguments));
    }

    /**
     * @return array
     */
    public function providerTestGetTypeChar()
    {
        return [
            [GeneratePassword::SPECIALS, [0]],
            [GeneratePassword::NUMBERS, [1]],
            [GeneratePassword::CAPITAL_LETTERS, [2]],
            [GeneratePassword::TINY_LETTERS, [3]],
            [GeneratePassword::NUMBERS, [13]],
        ];
    }

    /**
     * @return array
     */
    public function providerTestGetChar()
    {
        return [
            ['A', [GeneratePassword::CAPITAL_LETTERS, 1]],
            ['B', [GeneratePassword::CAPITAL_LETTERS, 2]],
            ['Y', [GeneratePassword::CAPITAL_LETTERS, 25]],
            ['Z', [GeneratePassword::CAPITAL_LETTERS, 26]],
            ['A', [GeneratePassword::CAPITAL_LETTERS, 27]],
            ['A', [GeneratePassword::CAPITAL_LETTERS, 53]],
        ];
    }
}
