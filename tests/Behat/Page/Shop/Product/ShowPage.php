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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'og-title' => 'synerise-og-title',
            'og-image' => 'synerise-og-image',
            'og-price' => 'synerise-og-price',
            'og-sale_price' => 'synerise-og-sale-price',
            'og-original_price' => 'synerise-og-original-price',
            'og-category' => 'synerise-og-category',
            'og-retailer_part_no' => 'synerise-og-retailer-part-no',
            'og-url' => 'synerise-og-url',
        ]);
    }

    public function hasOgTitle(): bool
    {
        return $this->hasElement('og-title');
    }

    public function hasOgImage(): bool
    {
        return $this->hasElement('og-image');
    }

    public function hasOgPrice(): bool
    {
        return $this->hasElement('og-price');
    }

    public function hasOgSalePrice(): bool
    {
        return $this->hasElement('og-sale_price');
    }

    public function hasOgOriginalPrice(): bool
    {
        return $this->hasElement('og-original_price');
    }

    public function hasOgCategory(): bool
    {
        return $this->hasElement('og-category');
    }

    public function hasOgRetailerPartNo(): bool
    {
        return $this->hasElement('og-retailer_part_no');
    }

    public function hasOgUrl(): bool
    {
        return $this->hasElement('og-url');
    }
}
