<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\Form\Setting;

use Application\Authentication\Command\UpdatePasswordCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class UpdatePasswordForm.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class UpdatePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('current', PasswordType::class, [
                'label' => 'authentication.forms.labels.current_password',
            ])
            ->add('new', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'les deux mot de passe doivent correspondre',
                'required' => true,
                'first_options' => [
                    'label' => 'authentication.forms.labels.password',
                ],
                'second_options' => [
                    'label' => 'authentication.forms.labels.password_confirm',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdatePasswordCommand::class,
            'translation_domain' => 'authentication',
        ]);
    }
}
