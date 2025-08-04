<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Page\Shop\Product\ShowPage;
use Webmozart\Assert\Assert;

final class OpenGraphContext implements Context
{
    public function __construct(
        private ShowPage $productShowPage,
    ) {
    }

    /**
     * @Then I should see meta tag :type
     */
    public function iShouldSeeMetaTag(string $type): void
    {
        $element = $this->productShowPage->getTag($type);

        Assert::notNull(
            $element,
            sprintf('Meta tag "%s" was not found', $type)
        );
    }

    /**
     * @Then I should see meta tag :type with value :value
     */
    public function iShouldSeeMetaTagWithValue(string $type, string $value): void
    {
        $element = $this->productShowPage->getTag($type);

        Assert::notNull(
            $element,
            sprintf('Meta tag "%s" was not found', $type)
        );

        Assert::same(
            $element->getAttribute('content'),
            $value,
            sprintf('Meta tag "%s" has unexpected content', $type)
        );
    }

    /**
     * @Then I should not see meta tag :type
     */
    public function iShouldNotSeeMetaTag(string $type): void
    {
        $element = $this->productShowPage->getTag($type);

        Assert::null(
            $element,
            sprintf('Meta tag "%s" exists but it should not', $type)
        );

    }
}
