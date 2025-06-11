<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAttributeChoiceType extends AbstractType
{
    /** @param RepositoryInterface<AttributeInterface> $attributeRepository */
    public function __construct(protected RepositoryInterface $attributeRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($attributes) {
                if (null === $attributes) {
                    return [];
                }

                if ($attributes instanceof Collection) {
                    return $attributes->map(function($attribute) {
                        return $attribute->getId();
                    })->toArray();
                }

                if (is_array($attributes)) {
                    return array_map(function($attribute) {
                        return $attribute->getId();
                    }, $attributes);
                }

                return [];

            },
            function ($attributeIds) {
                if (empty($attributeIds)) {
                    return null;
                }

                $attributes = [];
                foreach ($attributeIds as $id) {
                    $attribute = $this->attributeRepository->find($id);
                    if (null !== $attribute) {
                        $attributes[] = $attribute;
                    }
                }

                return $attributes;
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
        return 'synerise_integration_product_attribute_choice';
    }

    public function getChoices(): array
    {
        $attributes = $this->attributeRepository->findAll();

        $choices = [];
        foreach ($attributes as $attribute) {
            $choices[$attribute->getName()] = $attribute->getId();
        }

        return $choices;
    }
}
