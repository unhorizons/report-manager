<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form;

use Application\Report\Command\UpdateReportCommand;
use Infrastructure\Report\Symfony\Form\ValueObject\PeriodType;
use Infrastructure\Shared\Symfony\Form\Type\AutoGrowTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

/**
 * Class UpdateReportForm.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateReportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class)
            ->add('description', AutoGrowTextareaType::class)
            ->add('period', PeriodType::class)
            ->add('documents', DropzoneType::class, [
                'attr' => [
                    'accept' => 'application/pdf, application/x-pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ],
                'multiple' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateReportCommand::class,
            'translation_domain' => 'report',
        ]);
    }
}
