<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form\ValueObject;

use Domain\Report\ValueObject\Period;
use Infrastructure\Shared\Symfony\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IntervalDateType.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class IntervalDateType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('starting_at', DatePickerType::class)
            ->add('ending_at', DatePickerType::class)
            ->setDataMapper($this)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
            'empty_data' => null,
        ]);
    }

    /**
     * @param Period $viewData
     */
    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $forms = iterator_to_array($forms);
        $forms['starting_at']->setData($viewData->getStartingAt());
        $forms['ending_at']->setData($viewData->getEndingAt());
    }

    /**
     * @param Period $viewData
     */
    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        try {
            $viewData = Period::fromArray([
                $forms['starting_at']->getData(),
                $forms['ending_at']->getData(),
            ]);
        } catch (\InvalidArgumentException $e) {
            $forms['starting_at']->addError(new FormError($e->getMessage()));
            $forms['ending_at']->addError(new FormError($e->getMessage()));
        }
    }
}
