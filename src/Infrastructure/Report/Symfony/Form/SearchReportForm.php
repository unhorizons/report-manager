<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form;

use Application\Report\Command\SearchReportCommand;
use Infrastructure\Report\Symfony\Form\ValueObject\PeriodType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SearchReportForm.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SearchReportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => 'report.forms.labels.query',
                'help' => 'report.forms.labels.query_help',
            ])
            ->add('use_period', CheckboxType::class, [
                'label' => 'report.forms.labels.use_period',
                'required' => false,
            ])
            ->add('period', PeriodType::class, [
                'label' => 'report.forms.labels.period',
                'required' => false,
            ])
            ->add('seen', CheckboxType::class, [
                'label' => 'report.forms.labels.seen',
                'required' => false,
            ])
            ->add('unseen', CheckboxType::class, [
                'label' => 'report.forms.labels.unseen',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchReportCommand::class,
            'translation_domain' => 'report',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
