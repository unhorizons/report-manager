<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form;

use Application\Report\Command\UpdateEvaluationCommand;
use Infrastructure\Shared\Symfony\Form\Type\AutoGrowTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UpdateEvaluationForm.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateEvaluationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', AutoGrowTextareaType::class, [
            'label' => 'report.forms.labels.content',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateEvaluationCommand::class,
            'translation_domain' => 'report',
        ]);
    }
}
