<?php

namespace App\Checker;

class VirusChecker
{
    /**
     * @param \SplFileInfo $file
     *
     * @return bool
     */
    public function execute(\SplFileInfo $file)
    {
        // If virus, return false and delete file
        // Analyze returns always true for moment.
        return true;
    }
}
