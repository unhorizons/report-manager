<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\Controller;

use Application\Authentication\Command\GenerateGoogleAuthenticatorSecretCommand;
use Application\Authentication\Command\RegenerateBackupCodeCommand;
use Application\Authentication\Command\Toggle2FACommand;
use Application\Authentication\Command\UpdatePasswordCommand;
use Application\Authentication\Command\UpdateUserCommand;
use Domain\Authentication\Entity\User;
use Infrastructure\Authentication\Symfony\Form\Setting\Toggle2FaForm;
use Infrastructure\Authentication\Symfony\Form\Setting\UpdatePasswordForm;
use Infrastructure\Authentication\Symfony\Form\UpdateUserForm;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/profile/settings', name: 'authentication_setting_')]
final class SettingController extends AbstractController
{
    #[Route('/profile', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $command = new UpdateUserCommand($user);
        $form = $this->createForm(UpdateUserForm::class, $command, [
            'update_as_admin' => false,
        ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'authentication.flashes.profile_updated_successfully',
                    parameters: [],
                    domain: 'authentication'
                ));

                return $this->redirectSeeOther('authentication_setting_profile');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/authentication/setting/profile.html.twig',
            parameters: [
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/security', name: 'security', methods: ['GET', 'POST'])]
    public function security(): Response
    {
        return $this->render('domain/authentication/setting/security.html.twig');
    }

    #[Route('/security/password', name: 'security_password', methods: ['GET', 'POST'])]
    public function password(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $command = new UpdatePasswordCommand($user);
        $form = $this->createForm(UpdatePasswordForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'authentication.flashes.reset_password_confirmed_successfully',
                    parameters: [],
                    domain: 'authentication'
                ));

                return $this->redirectSeeOther('authentication_setting_security');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/authentication/setting/password.html.twig',
            parameters: [
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/security/2fa', name: 'security_2fa', methods: ['GET', 'POST'])]
    public function twoFactorAuthentication(GoogleAuthenticatorInterface $authenticator, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (empty($user->getGoogleAuthenticatorSecret())) {
            try {
                $this->dispatchSync(new GenerateGoogleAuthenticatorSecretCommand($user));
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        }

        if (empty($user->getBackupCode())) {
            try {
                $this->dispatchSync(new RegenerateBackupCodeCommand($user));
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        }

        $command = new Toggle2FACommand(
            user: $user,
            google: $user->isGoogleAuthenticatorEnabled(),
            email: $user->isEmailAuthEnabled()
        );

        $form = $this->createForm(Toggle2FaForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', 'Paramètres 2FA modifiés');

                return $this->redirectSeeOther('authentication_settings_2fa');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $qrcode = $authenticator->getQRContent($user);

        return $this->render(
            view: 'domain/authentication/setting/2fa.html.twig',
            parameters: [
                'qrcode_content' => $qrcode,
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }
}
