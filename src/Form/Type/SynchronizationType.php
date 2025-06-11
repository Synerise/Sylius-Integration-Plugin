<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SynchronizationType extends AbstractResourceType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, string $dataClass, array $validationGroups = [])
    {
        parent::__construct($dataClass, $validationGroups);
        $this->translator = $translator;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', SynchronizationTypeChoiceType::class, [
                'label' => 'synerise_integration.ui.synchronization.form.type.label',
                'choice_translation_domain' => true,
                'translation_domain' => 'messages',
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('synerise_integration.ui.synchronization.form.type.errors.not_null')
                    ])
                ],
                'expanded' => true,
                'required' => true,
                'multiple' => false
            ])
            ->add('sinceWhen', DateType::class, [
                'label' => 'synerise_integration.ui.synchronization.form.since_when.label',
                'widget' => 'single_text',
                'translation_domain' => 'messages',
                'choice_translation_domain' => true,
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('synerise_integration.ui.synchronization.form.since_when.errors.not_null')
                    ]),
                    new LessThanOrEqual([
                        'propertyPath' => 'parent.all[untilWhen].data',
                        'message' => $this->translator->trans('synerise_integration.ui.synchronization.form.since_when.errors.less_than_or_equal')
                    ])

                ],
                'attr' => [
                    'max' => (new \DateTime())->format('Y-m-d')
                ]
            ])
            ->add('untilWhen', DateType::class, [
                'label' => 'synerise_integration.ui.synchronization.form.until_when.label',
                'widget' => 'single_text',
                'constraints' => [
                    new NotNull([
                        'message' => $this->translator->trans('synerise_integration.ui.synchronization.form.until_when.errors.not_null')
                    ]),
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[sinceWhen].data',
                        'message' => $this->translator->trans('synerise_integration.ui.synchronization.form.until_when.errors.greater_than_or_equal')
                    ])
                ],
                'attr' => [
                    'max' => (new \DateTime())->format('Y-m-d')
                ]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization';
    }
}
