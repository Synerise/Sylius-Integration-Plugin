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

    public function getTag(string $property): ?\Behat\Mink\Element\NodeElement
    {
        return $this->getSession()->getPage()->find(
            'xpath',
            '//head/meta[@property="'.$property.'"]'
        );
    }
}
