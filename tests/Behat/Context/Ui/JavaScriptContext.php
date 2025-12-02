<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\When;

final class JavaScriptContext extends RawMinkContext
{
    /**
     * @When I wait for :locator element
     */
    public function IWaitFor(string $locator): void
    {
        $this->getSession()->wait(10000, "document.querySelector('{$locator}') !== null");
    }

    /**
     * @When I click :locator element
     */
    public function iClick(string $locator): void
    {
        $element = $this->getSession()->getPage()->find('css', $locator);
        $element->click();
    }
}
