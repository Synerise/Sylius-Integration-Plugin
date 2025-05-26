<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;

class ProductAttributeValueChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_translation_domain' => true,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_product_attribute_value_choice';
    }

    private function getChoices(): array
    {
        $choices = [];
        foreach (ProductAttributeValue::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $choices;
    }
}
