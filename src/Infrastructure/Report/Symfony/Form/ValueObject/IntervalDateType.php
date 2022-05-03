<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form\ValueObject;

use Domain\Report\ValueObject\IntervalDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IntervalDateType
 * @package Infrastructure\Report\Symfony\Form\ValueObject
 * @author bernard-ng <bernard@devscast.tech>
 */
final class IntervalDateType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('starting_at', DateTimeType::class)
            ->add('ending_at', DateTimeType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => IntervalDate::class,
            'empty_data' => null,
        ]);

        return $resolver;
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms)
    {
        $forms = iterator_to_array($forms);
        $forms['starting_at']->setData($viewData->getStartingAt());
        $forms['ending_at']->setData($viewData->getEndingAt());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData)
    {
        $forms = iterator_to_array($forms);
        try {
            $viewData = IntervalDate::fromArray([
                $forms['starting_at']->getData(),
                $forms['ending_at']->getData()
            ]);
        } catch (\InvalidArgumentException $e) {
            $forms['starting_at']->addError(new FormError($e->getMessage()));
            $forms['ending_at']->addError(new FormError($e->getMessage()));
        }
    }
}
