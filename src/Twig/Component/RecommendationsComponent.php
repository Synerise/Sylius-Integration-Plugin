<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\Api\Recommendations\Models\PostRecommendationsRequest;
use Synerise\Api\Recommendations\Models\RecommendationResponseSchemaV2Materializer;
use Synerise\Sdk\Api\ClientBuilderFactory;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\ProfileManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Webmozart\Assert\Assert;

class RecommendationsComponent
{
    use HookableComponentTrait;

    public const DEFAULT_LIMIT = 8;

    public ?string $campaignId = null;

    public ?string $correlationId = null;

    public ?OrderInterface $cart = null;

    public bool $showForEmptyCart = false;

    public ?ProductInterface $product = null;

    public int $limit = self::DEFAULT_LIMIT;

    /**
     * @param ProductRepository<ProductInterface> $productRepository
     * */
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
            $channel = $this->channelConfiguration?->getChannel();
            if ($channel != null) {
                if ($recommendations = $this->getRecommendations()) {
                    if ($data = $recommendations->getData()) {
                        $skus = [];
                        foreach ($data as $recommendation) {
                            $skus[] = $recommendation->getItemId();
                        }

                        $this->correlationId = $recommendations->getExtras()?->getCorrelationId();
                        return $this->findBySkus(
                            $channel,
                            $this->localeContext->getLocaleCode(),
                            $skus,
                            $this->limit
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            $this->syneriseLogger->error($e);
        }

        return [];
    }

    #[ExposeInTemplate(name: 'campaignId')]
    public function getCampaignId(): ?string
    {
        return $this->campaignId;
    }

    #[ExposeInTemplate(name: 'correlationId')]
    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    protected function findBySkus(ChannelInterface $channel, string $locale, array $skus, int $count): array
    {
        $results = $this->productRepository->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.code IN (:codes)')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('channel', $channel)
            ->setParameter('codes', $skus)
            ->setParameter('locale', $locale)
            ->setParameter('enabled', true)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($results);
        return $results;
    }

    /**
     * @throws NotFoundException|\Exception
     */
    protected function getRecommendations(): ?RecommendationResponseSchemaV2Materializer
    {
        $config = $this->channelConfiguration?->getWorkspace();
        if ($config == null) {
            return null;
        }

        $cartItems = $this->cart?->getItems();
        if ($cartItems != null && $cartItems->count() == 0 && !$this->showForEmptyCart) {
            return null;
        }

        $uuid = $this->profileManager->getProfile()->getUuid();
        $client = $this->clientBuilderFactory->create($config);
        if ($uuid != null && $this->campaignId != null && $client != null) {
            $postRecommendationsRequest = new PostRecommendationsRequest();
            $postRecommendationsRequest->setCampaignId($this->campaignId);
            $postRecommendationsRequest->setClientUUID($uuid);

            if ($code = $this->product?->getCode()) {
                $postRecommendationsRequest->setItems([ $code ]);
            }

            if ($cartItems != null) {
                /** @var string[] $skus */
                $skus = [];

                foreach ($cartItems as $cartItem) {
                    $sku = $cartItem->getProduct()?->getCode();
                    if ($sku != null) {
                        $skus[] = $sku;
                    }

                }
                $postRecommendationsRequest->setItems($skus);
            }

            return $client->recommendations()->recommendations()->v2()->recommend()->campaigns()
                ->post($postRecommendationsRequest)->wait();
        }

        return null;
    }
}
