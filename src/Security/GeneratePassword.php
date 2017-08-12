<?php

namespace App\Security;

class GeneratePassword
{
    const PASSWORD_LENGTH = 8;

    const SPECIALS = [40, 47];
    const NUMBERS = [48, 57];
    const CAPITAL_LETTERS = [65, 90];
    const TINY_LETTERS = [97, 122];

    const TYPES_CHARS = [self::SPECIALS, self::NUMBERS, self::CAPITAL_LETTERS, self::TINY_LETTERS];

    /**
     * @return string
     */
    public function execute(): string
    {
        $chars = [];

        $sizeTypes = count(self::TYPES_CHARS);
        for ($i = 0; $i < $sizeTypes; ++$i) {
            $chars[] = $this->getChar(self::TYPES_CHARS[$i], random_int(1, 1000));
        }

        for ($i = $sizeTypes; $i < self::PASSWORD_LENGTH; ++$i) {
            $chars[] = $this->getChar($this->getTypeChar(random_int(1, 1000)), random_int(1, 1000));
        }

        shuffle($chars);

        return implode('', $chars);
    }

    /**
     * @param array $ascii
     * @param int   $random
     *
     * @return string
     */
    private function getChar(array $ascii, int $random): string
    {
        $size = $ascii[1] - $ascii[0] + 1;
        $index = $ascii[0] + (($random - 1) % $size);

        return chr($index);
    }

    /**
     * @param int $random
     *
     * @return array
     */
    private function getTypeChar(int $random): array
    {
        $index = $random % count(self::TYPES_CHARS);

        return self::TYPES_CHARS[$index];
    }
}
