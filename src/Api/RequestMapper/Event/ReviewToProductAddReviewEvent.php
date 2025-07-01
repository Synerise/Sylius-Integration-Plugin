<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\CustomEvent;
use Synerise\Sdk\Api\RequestBody\Events\AddedReviewBuilder;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\SyliusIntegrationPlugin\Helper\ProductUrlHelper;
use Webmozart\Assert\Assert;

class ReviewToProductAddReviewEvent
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ProductUrlHelper $productUrlHelper,
    ) {
    }

    public function prepare(ReviewInterface $review, Client $client): CustomEvent
    {
        $product = $review->getReviewSubject();
        Assert::isInstanceOf($product, \Sylius\Component\Core\Model\ProductInterface::class);

        /** @var TaxonInterface $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        $taxons = [];
        foreach ($product->getProductTaxons() as $productTaxon) {
            if ($productTaxon->getTaxon()) {
                /** @var array<string> $taxons */
                $taxons[] = $productTaxon->getTaxon()->getFullname(' > ');
            }
        }

        $productAddReviewEvent = AddedReviewBuilder::initialize($client)
            ->setTime($review->getCreatedAt() ?: new \DateTime())
            ->setRating($review->getRating())
            ->setComment($review->getComment())
            ->setTitle($review->getTitle())
            ->setSku($product->getCode())
            ->setCategory($mainTaxon->getFullname(' > '))
            ->setCategories($taxons)
            ->setUrl($this->productUrlHelper->generate($product))
            ->setParam('status', $review->getStatus())
            ->build();

        $genericEvent = new GenericEvent($productAddReviewEvent, ['review' => $review]);

        $this->eventDispatcher->dispatch(
            $genericEvent,
            sprintf('synerise.%s.prepare', AddedToCartBuilder::ACTION),
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }
}
