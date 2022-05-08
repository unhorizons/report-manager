<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form;

use Application\Report\Command\CreateEvaluationCommand;
use Infrastructure\Shared\Symfony\Form\Type\AutoGrowTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CreateEvaluationForm.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CreateEvaluationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', AutoGrowTextareaType::class, [
            'label' => 'report.forms.labels.content',
            'help' => 'report.forms.labels.content_help',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateEvaluationCommand::class,
            'translation_domain' => 'report',
        ]);
    }
}
