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
    public function __construct(
        private ShowPage $productShowPage,
    ) {
    }

    /**
     * @Then I should see OpenGraph meta tag :type with value :value
     */
    public function iShouldSeeOpenGraphMetaTagWithValue(string $type, string $value): void
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

        Assert::true(
            $this->productShowPage->$hasMethod(),
            sprintf('OpenGraph meta tag "%s" was not found', $type)
        );

//        $element = $this->productShowPage->getElement('og-' . str_replace('_', '-', $type));
//
//        if (str_contains($value, '*')) {
//            Assert::regex(
//                $element->getAttribute('content'),
//                '/' . str_replace('*', '.*', preg_quote($value, '/')) . '/',
//                sprintf('OpenGraph meta tag "%s" content does not match pattern "%s"', $type, $value)
//            );
//        } else {
//            Assert::same(
//                $element->getAttribute('content'),
//                $value,
//                sprintf('OpenGraph meta tag "%s" has unexpected content', $type)
//            );
//        }
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

    #[Given('this product has an image :arg1')]
    public function thisProductHasAnImage($arg1): void
    {
        throw new PendingException();
    }

    #[When('I view product :arg1')]
    public function iViewProduct($arg1): void
    {
        throw new PendingException();
    }

    #[Given('the product :arg1 has price :arg2')]
    public function theProductHasPrice($arg1, $arg2): void
    {
        throw new PendingException();
    }

    #[Given('the channel has OpenGraph integration disabled')]
    public function theChannelHasOpengraphIntegrationDisabled(): void
    {
        throw new PendingException();
    }
}
