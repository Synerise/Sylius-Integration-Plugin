<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\Given;
use Behat\Step\When;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Page\Shop\Product\ShowPage;
use Webmozart\Assert\Assert;

final class OpenGraphContext implements Context
{
    private $hasMethods = [
        'title' => 'hasOgTitle',
        'image' => 'hasOgImage',
        'price' => 'hasOgPrice',
        'sale_price' => 'hasOgSalePrice',
        'original_price' => 'hasOgOriginalPrice',
        'category' => 'hasOgCategory',
        'retailer_part_no' => 'hasOgRetailerPartNo',
        'url' => 'hasOgUrl',
]   ;

    public function __construct(
        private ShowPage $productShowPage,
    ) {
    }

    /**
     * @Then I should see OpenGraph meta tag :type
     */
    public function iShouldSeeOpenGraphMetaTag(string $type): void
    {
        if (!isset($this->hasMethods[$type])) {
            throw new \InvalidArgumentException(sprintf('Unknown OpenGraph meta tag type "%s"', $type));
        }

        $hasMethod = $this->hasMethods[$type];

        Assert::true(
            $this->productShowPage->$hasMethod(),
            sprintf('OpenGraph meta tag "%s" was not found', $type)
        );
    }

    /**
     * @Then I should see OpenGraph meta tag :type with value :value
     */
    public function iShouldSeeOpenGraphMetaTagWithValue(string $type, string $value): void
    {
        if (!isset($this->hasMethods[$type])) {
            throw new \InvalidArgumentException(sprintf('Unknown OpenGraph meta tag type "%s"', $type));
        }

        $hasMethod = $this->hasMethods[$type];

        Assert::true(
            $this->productShowPage->$hasMethod(),
            sprintf('OpenGraph meta tag "%s" was not found', $type)
        );

        $element = $this->productShowPage->getOgTag($type);

        if (str_contains($value, '*')) {
            Assert::regex(
                $element->getAttribute('content'),
                '/' . str_replace('*', '.*', preg_quote($value, '/')) . '/',
                sprintf('OpenGraph meta tag "%s" content does not match pattern "%s"', $type, $value)
            );
        } else {
            Assert::same(
                $element->getAttribute('content'),
                $value,
                sprintf('OpenGraph meta tag "%s" has unexpected content', $type)
            );
        }
    }

    /**
     * @Then I should not see OpenGraph meta tag :type
     */
    public function iShouldNotSeeOpenGraphMetaTag(string $type): void
    {
        $hasMethod = match ($type) {
            'title' => 'hasOgTitle',
            'image' => 'hasOgImage',
            'price' => 'hasOgPrice',
            'sale_price' => 'hasOgSalePrice',
            'original_price' => 'hasOgOriginalPrice',
            'category' => 'hasOgCategory',
            'retailer_part_no' => 'hasOgRetailerPartNo',
            'url' => 'hasOgUrl',
            default => throw new \InvalidArgumentException(sprintf('Unknown OpenGraph meta tag type "%s"', $type)),
        };

        Assert::false(
            $this->productShowPage->$hasMethod(),
            sprintf('OpenGraph meta tag "%s" exists but it should not', $type)
        );
    }
}
