<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;

class SynchronizationTypeChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($synchronizationTypeValue) {
                if ($synchronizationTypeValue === null) {
                    return '';
                }
                return $synchronizationTypeValue->value;
            },
            function ($value) {
                if (empty($value)) {
                    return null;
                }
                return SynchronizationDataType::from($value);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization_type_choice';
    }

    private function getChoices(): array
    {
        $choices = [];
        foreach (SynchronizationDataType::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $choices;
    }
}
