<?php

use Alex\MailCatcher\Behat\MailCatcherContext as BaseMailCatcherContext;
use Alex\MailCatcher\Message;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\DomCrawler\Crawler;

class MailCatcherContext extends BaseMailCatcherContext
{
    /** @var MinkContext */
    private $minkContext;

    /** @var string */
    private $currentPassword;

    /**
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    public function getContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @param string $link
     *
     * @throws \Exception
     *
     * @When /^I follow "(?P<link>(?:[^"]|\\")*)" in mail$/
     */
    public function iFollowInMail($link)
    {
        $linkFound = $this->getCrawler($this->currentMessage)->selectLink($link);

        if (0 === $linkFound->count()) {
            throw new \Exception(sprintf('Link %s not found in current page', $link));
        }

        $this->minkContext->visit($linkFound->attr('href'));
    }

    /**
     * @throws \Exception
     *
     * @When /^I save password in mail$/
     */
    public function iSavePasswordInMail()
    {
        $matches = [];
        preg_match('#<li>Mot de passe : (.*)</li>#', $this->currentMessage->getContent(), $matches);
        if (2 !== count($matches)) {
            throw new \Exception('Password not found in mail');
        }

        $this->currentPassword = $matches[1];
    }

    /**
     * @param string $field
     *
     * @When /^I fill in "(?P<field>(?:[^"]|\\")*)" with password saved from mail$/
     */
    public function fillFieldWithPasswordSavedFromMail(string $field)
    {
        $this->minkContext->fillField($field, $this->currentPassword);
    }

    /**
     * @param Message $message
     *
     * @return Crawler
     */
    private function getCrawler(Message $message)
    {
        if (!class_exists('Symfony\Component\DomCrawler\Crawler')) {
            throw new \RuntimeException('Can\'t crawl HTML: Symfony DomCrawler component is missing from autoloading.');
        }

        if (!$message->isMultipart()) {
            $content = $message->getContent();
        } elseif ($message->hasPart('text/html')) {
            $content = $this->getCrawler($message)->text();
        } elseif ($message->hasPart('text/plain')) {
            $content = $message->getPart('text/plain')->getContent();
        } else {
            throw new \RuntimeException(sprintf('Unable to read mail'));
        }

        return new Crawler($content);
    }
}
