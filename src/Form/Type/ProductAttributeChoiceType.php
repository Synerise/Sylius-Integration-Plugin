<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAttributeChoiceType extends AbstractType
{
    /** @param RepositoryInterface<AttributeInterface> $attributeRepository */
    public function __construct(protected RepositoryInterface $attributeRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_translation_domain' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_product_attribute_choice';
    }

    private function getChoices(): array
    {
        $options = [];

        foreach ($this->attributeRepository->findAll() as $entity) {
            $options[$entity->getName()] = $entity->getCode();
        }

        return $options;
    }
}
