<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;

final class SynchronizationConfigurationType extends AbstractResourceType
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param ChannelConfigurationRepositoryInterface<ChannelConfigurationInterface> $channelConfigurationRepository
     * @param SynchronizationConfigurationRepositoryInterface<SynchronizationConfigurationInterface> $synchronizationConfigurationRepository
     */
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ChannelConfigurationRepositoryInterface $channelConfigurationRepository,
        private SynchronizationConfigurationRepositoryInterface $synchronizationConfigurationRepository,
        string $dataClass,
        array $validationGroups = []
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SynchronizationConfigurationInterface $data */
        $data = $options['data'];

        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.channel.label',
                'choices' => $this->getAvailableChannels($data->getId()),
            ])
            ->add('productAttributes', ProductAttributeChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.product_attributes.label',
                'placeholder' => 'synerise_integration.synchronization_configuration.form.product_attributes.placeholder',
                'choice_translation_domain' => true,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-controller' => 'multiselect',
                ],
            ])
            ->add('productAttributeValue', ProductAttributeValueChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.product_attributes_value.label',
                'help' => 'synerise_integration.synchronization_configuration.form.product_attributes_value.help',
                'choice_translation_domain' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'synerise_integration.synchronization_configuration.product_attributes_value.not_null',
                    ]),
                ],
                'expanded' => true,
                'required' => true,
                'multiple' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization_configuration';
    }

    private function getAvailableChannels(?int $currentId = null): array
    {
        return $this->getChannelsByIds(array_diff(
            $this->getChannelIdsFromChannelConfigurations(),
            $this->getChannelIdsFromSynchronizationConfigurations($currentId)
        ));
    }

    private function getChannelsByIds(array $ids): array
    {
        return $this->channelRepository->findBy(['id' => $ids]);
    }

    private function getChannelIdsFromChannelConfigurations(): array
    {
        return array_map(
        fn ($configuration) => $configuration->getChannel()?->getId(),
            $this->channelConfigurationRepository->findAllExceptId()
        );
    }

    private function getChannelIdsFromSynchronizationConfigurations(?int $currentId = null): array
    {
        return array_map(
            fn ($configuration) => $configuration->getChannel()->getId(),
            $this->synchronizationConfigurationRepository->findAllExceptId($currentId)
        );
    }
}
