<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\Api\Recommendations\Models\PostRecommendationsRequest;
use Synerise\Api\Recommendations\Models\RecommendationResponseSchemaV2Materializer;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\ProfileManager;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class RecommendationsComponent
{
    use HookableComponentTrait;

    public const DEFAULT_LIMIT = 8;

    public ?string $campaignId = null;

    public ?OrderInterface $cart = null;

    public bool $showForEmptyCart = false;

    public ?ProductInterface $product = null;

    public int $limit = self::DEFAULT_LIMIT;

    public function __construct(
        protected readonly LoggerInterface                $syneriseLogger,
        protected readonly ProductRepositoryInterface     $productRepository,
        protected readonly LocaleContextInterface         $localeContext,
        protected readonly ProfileManager                 $profileManager,
        protected readonly ?ChannelConfigurationInterface $channelConfiguration,
        protected readonly ClientBuilderFactory           $clientBuilderFactory,
    ) {
    }

    #[ExposeInTemplate(name: 'products')]
    public function getProducts(): array
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelConfiguration?->getChannel();
            $localeCode = $this->localeContext->getLocaleCode();
            $uuid = $this->profileManager->getProfile()->getUuid();

            $client = $this->clientBuilderFactory->create($this->channelConfiguration?->getWorkspace());
            if ($uuid != null && $this->campaignId != null && $channel != null && $client != null) {
                if ($recommendations = $this->getRecommendations()) {
                    $skus = [];
                    foreach ($recommendations->getData() as $recommendation) {
                        $skus[] = $recommendation->getItemId();
                    }

                    return $this->findBySkus($channel, $localeCode, $skus, $this->limit);
                }
            }
        } catch (\Exception $e) {
            $this->syneriseLogger->error($e);
        }

        return [];
    }

    protected function findBySkus(ChannelInterface $channel, string $locale, array $skus, int $count): array
    {
        return $this->productRepository->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.code IN (:codes)')
            ->andWhere('o.enabled = :enabled')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('codes', $skus)
            ->setParameter('locale', $locale)
            ->setParameter('enabled', true)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @throws NotFoundException|\Exception
     */
    protected function getRecommendations(): ?RecommendationResponseSchemaV2Materializer
    {
        $cartItems = $this->cart?->getItems();

        if ($cartItems != null && $cartItems->count() == 0 && !$this->showForEmptyCart) {
            return null;
        }

        $uuid = $this->profileManager->getProfile()->getUuid();
        $client = $this->clientBuilderFactory->create($this->channelConfiguration?->getWorkspace());
        if ($uuid != null && $this->campaignId != null && $client != null) {
            $postRecommendationsRequest = new PostRecommendationsRequest();
            $postRecommendationsRequest->setCampaignId($this->campaignId);
            $postRecommendationsRequest->setClientUUID($uuid);

            if ($code = $this->product?->getCode()) {
                $postRecommendationsRequest->setItems([ $code ]);
            }

            if ($cartItems != null) {
                $skus = [];
                foreach ($cartItems as $cartItem) {
                    $skus[] = $cartItem->getProduct()->getCode();
                }
                $postRecommendationsRequest->setItems($skus);
            }

            return $client->recommendations()->recommendations()->v2()->recommend()->campaigns()
                ->post($postRecommendationsRequest)->wait();
        }

        return null;
    }
}
