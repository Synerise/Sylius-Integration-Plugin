<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Webmozart\Assert\Assert;

class TrackingScriptContext extends RawMinkContext implements Context
{
    /**
     * @When I visit a page
     */
    public function iVisitAPage(): void
    {
        // Visit a page that renders the tracking script component
        $this->visitPath('/');
    }

    /**
     * @Then I should see the tracking script in the page source
     */
    public function iShouldSeeTheTrackingScriptInThePageSource(): void
    {
        $pageSource = $this->getSession()->getPage()->getContent();

        Assert::contains(
            $pageSource,
            'SyneriseObjectNamespace',
            'Tracking script should be present in page source'
        );

        Assert::contains(
            $pageSource,
            'SR.init(',
            'Script should contain SR.init call'
        );
    }

    /**
     * @Then I should not see the tracking script in the page source
     */
    public function iShouldNotSeeTheTrackingScriptInThePageSource(): void
    {
        $pageSource = $this->getSession()->getPage()->getContent();

        Assert::notContains(
            $pageSource,
            'SyneriseObjectNamespace',
            'Tracking script should not be present in page source'
        );
    }

    /**
     * @Then the script should contain the tracker host :host
     */
    public function theScriptShouldContainTheTrackerHost(string $host): void
    {
        $pageSource = $this->getSession()->getPage()->getContent();

        Assert::contains(
            $pageSource,
            "https://{$host}/synerise-javascript-sdk.min.js",
            "Script should load from tracker host: {$host}"
        );
    }

    /**
     * @Then the script should initialize with correct options:
     */
    public function theScriptShouldInitializeWithCorrectOptions(TableNode $table): void
    {
        $pageSource = $this->getSession()->getPage()->getContent();

        // Extract the options JSON from SR.init() call
        preg_match('/SR\.init\((.*?)\);/', $pageSource, $matches);
        Assert::notEmpty($matches, 'Could not find SR.init() call in page source');

        $optionsJson = $matches[1];
        $options = json_decode($optionsJson, true);
        Assert::isArray($options, 'Options should be valid JSON');

        foreach ($table->getHash() as $row) {
            $option = $row['option'];
            $expectedValue = $row['value'];

            Assert::keyExists($options, $option, "Option '{$option}' should be present");

            // Handle different value types
            if ($expectedValue === 'true') {
                Assert::true($options[$option], "Option '{$option}' should be true");
            } elseif ($expectedValue === 'false') {
                Assert::false($options[$option], "Option '{$option}' should be false");
            } elseif (str_starts_with($expectedValue, '{')) {
                // JSON object
                $expectedArray = json_decode($expectedValue, true);
                Assert::eq(
                    $options[$option],
                    $expectedArray,
                    "Option '{$option}' should match expected JSON"
                );
            } else {
                Assert::eq(
                    $options[$option],
                    $expectedValue,
                    "Option '{$option}' should equal '{$expectedValue}'"
                );
            }
        }
    }
}
