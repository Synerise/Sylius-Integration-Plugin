<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Unit\Twig\Component;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Helper\ProductDataFormatter;
use Synerise\SyliusIntegrationPlugin\Twig\Component\OpenGraphComponent;

class OpenGraphComponentTest extends TestCase
{
    private OpenGraphComponent $openGraphComponent;
    private ProductVariantPricesCalculatorInterface $pricesCalculator;
    private ChannelContextInterface $channelContext;
    private CurrencyContextInterface $currencyContext;
    private CurrencyConverterInterface $currencyConverter;
    private ChannelConfigurationInterface $channelConfiguration;
    private ProductDataFormatter $formatter;

    protected function setUp(): void
    {
        $this->pricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->currencyContext = $this->createMock(CurrencyContextInterface::class);
        $this->currencyConverter = $this->createMock(CurrencyConverterInterface::class);
        $this->channelConfiguration = $this->createMock(ChannelConfigurationInterface::class);
        $this->formatter = $this->createMock(ProductDataFormatter::class);

        $this->openGraphComponent = new OpenGraphComponent(
            $this->pricesCalculator,
            $this->channelContext,
            $this->currencyContext,
            $this->currencyConverter,
            $this->formatter,
            $this->channelConfiguration
        );
    }

    public function testConvertPrice_WhenBaseCurrencyIsSetButConversionCurrencyIsTheSame(): void
    {
        // Create proper mocks
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);

        // Setup currency mock
        $currency->method('getCode')->willReturn('USD');

        // Setup channel mock
        $channel->method('getBaseCurrency')->willReturn($currency);
        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->currencyContext->method('getCurrencyCode')->willReturn('USD');

        // Setup currency converter mock for same currency
        $this->currencyConverter
            ->method('convert')
            ->with(100, 'USD', 'USD')
            ->willReturn(100);

        // Test the private method through reflection
        $reflectionClass = new \ReflectionClass(OpenGraphComponent::class);
        $method = $reflectionClass->getMethod('convertPrice');
        $method->setAccessible(true);

        $result = $method->invoke($this->openGraphComponent, 100);

        $this->assertEquals(100, $result);
    }

    public function testConvertPrice_WhenBaseCurrencyAndConversionCurrencyDiffer(): void
    {
        // Create proper mocks
        $channel = $this->createMock(ChannelInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);

        // Setup currency mock
        $currency->method('getCode')->willReturn('EUR');

        // Setup channel mock
        $channel->method('getBaseCurrency')->willReturn($currency);
        $this->channelContext->method('getChannel')->willReturn($channel);
        $this->currencyContext->method('getCurrencyCode')->willReturn('USD');

        // Setup currency converter mock for different currencies
        $this->currencyConverter
            ->method('convert')
            ->with(100, 'EUR', 'USD')
            ->willReturn(85);

        // Test the private method through reflection
        $reflectionClass = new \ReflectionClass(OpenGraphComponent::class);
        $method = $reflectionClass->getMethod('convertPrice');
        $method->setAccessible(true);

        $result = $method->invoke($this->openGraphComponent, 100);

        $this->assertEquals(85, $result);
    }
}
