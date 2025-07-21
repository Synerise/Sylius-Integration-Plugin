<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Page\Shop\Product;

use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;
use Behat\Mink\Session;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends BaseShowPage
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        SummaryPageInterface $summaryPage
    ) {
        parent::__construct($session, $minkParameters, $router, $summaryPage);
    }

    public function hasOgTitle(): bool
    {
        return $this->getOgTag('title')?->getAttribute('content') !== null;
    }

    public function hasOgImage(): bool
    {
        return $this->getOgTag('image')?->getAttribute('content') !== null;
    }

    public function hasOgPrice(): bool
    {
        return $this->getProductTag('price:amount')?->getAttribute('content') !== null;
    }

    public function hasOgSalePrice(): bool
    {
        return $this->getProductTag('sale_price:amount')?->getAttribute('content') !== null;
    }

    public function hasOgOriginalPrice(): bool
    {
        return $this->getProductTag('original_price:amount')?->getAttribute('content') !== null;
    }

    public function hasOgCategory(): bool
    {
        return $this->getOgTag('category')?->getAttribute('content') !== null;
    }

    public function hasOgRetailerPartNo(): bool
    {
        return $this->getOgTag('retailer_part_no')?->getAttribute('content') !== null;
    }

    public function hasOgUrl(): bool
    {
        return $this->getOgTag('url')?->getAttribute('content') !== null;
    }

    public function getOgTag(string $property): ?\Behat\Mink\Element\NodeElement
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@property="og:'.$property.'"]'
        );
    }

    public function getProductTag(string $property): ?\Behat\Mink\Element\NodeElement
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@property="product:'.$property.'"]'
        );
    }
}
