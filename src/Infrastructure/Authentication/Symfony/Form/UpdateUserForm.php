<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\Form;

use Application\Authentication\Command\UpdateUserCommand;
use Infrastructure\Authentication\Symfony\Form\ValueObject\GenderType;
use Infrastructure\Authentication\Symfony\Form\ValueObject\RolesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class RegisterUserForm.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdateUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $updateAsAdmin = $options['update_as_admin'];

        $builder
            ->add('email', EmailType::class, [
                'label' => 'authentication.forms.labels.email',
            ])
            ->add('username', TextType::class, [
                'label' => 'authentication.forms.labels.username',
            ])
            ->add('job_title', TextType::class, [
                'label' => 'authentication.forms.labels.job_title',
            ])
            ->add('gender', GenderType::class, [
                'label' => 'authentication.forms.labels.gender',
            ]);

        if ($updateAsAdmin) {
            $builder->add('roles', RolesType::class, [
                'label' => 'authentication.forms.labels.roles',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'update_as_admin' => true,
            'data_class' => UpdateUserCommand::class,
            'translation_domain' => 'authentication',
        ]);
        $resolver->setAllowedTypes('update_as_admin', ['bool']);
    }
}
