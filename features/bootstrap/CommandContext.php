<?php

use Behat\Behat\Context\Context;

class CommandContext implements Context
{
    /** @var string */
    private $output;

    /**
     * @param string $command
     *
     * @When I run the app command :command
     */
    public function iRunTheAppCommand(string $command)
    {
        $this->output = shell_exec(sprintf('php bin/console %s --env=test', $command));
    }

    /**
     * @param string $string
     *
     * @throws \Exception
     *
     * @Then I should see :string in the output of command
     */
    public function iShouldSeeInTheOutputOfCommand(string $string)
    {
        if (strpos($this->output, $string) === false) {
            throw new \Exception(sprintf('Did not see "%s" in output "%s"', $string, $this->output));
        }
    }
}
