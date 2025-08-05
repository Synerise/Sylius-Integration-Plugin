<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

final class SynchronizationConfigurationType extends AbstractResourceType
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private EntityRepository $channelConfigurationRepository,
        private EntityRepository $synchronizationConfigurationRepository,
        string $dataClass,
        array $validationGroups = []
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.channel.label',
                'choices' => $this->getAvailableChannels($options["data"]->getChannel()?->getId()),
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

    private function getAvailableChannels(?int $currentChannelId): array
    {
        $configuredChannels = $this->channelConfigurationRepository->findAll();
        $configuredChannelIds = array_map(fn ($ch) => $ch->getChannel()->getId(), $configuredChannels);

        $configuredSynchronizations = $this->synchronizationConfigurationRepository->findAll();
        $configuredSynchChannelIds = array_map(fn ($ch) => $ch->getChannel()->getId(), $configuredSynchronizations);

        $availableIds = array_diff($configuredChannelIds, $configuredSynchChannelIds);
        if (null !== $currentChannelId) {
            $availableIds[] = $currentChannelId;
        }

        $availableChannels = $this->channelRepository->findBy(['id' => $availableIds]);

        return $availableChannels;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization_configuration';
    }
}
