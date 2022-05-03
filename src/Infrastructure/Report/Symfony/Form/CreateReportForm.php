<?php

declare(strict_types=1);

namespace Infrastructure\Report\Symfony\Form;

use Application\Report\Command\CreateReportCommand;
use Infrastructure\Report\Symfony\Form\ValueObject\IntervalDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

/**
 * Class CreateReportForm
 * @package Infrastructure\Report\Symfony\Form
 * @author bernard-ng <bernard@devscast.tech>
 */
final class CreateReportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('interval_date', IntervalDateType::class)
            ->add('document_file', DropzoneType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateReportCommand::class,
            'translation_domain' => 'report',
        ]);
    }
}
