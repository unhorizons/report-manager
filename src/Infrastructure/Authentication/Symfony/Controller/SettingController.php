<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\Controller;

use Application\Authentication\Command\GenerateGoogleAuthenticatorSecretCommand;
use Application\Authentication\Command\RegenerateBackupCodeCommand;
use Application\Authentication\Command\Toggle2FACommand;
use Domain\Authentication\Entity\User;
use Infrastructure\Authentication\Symfony\Form\Setting\Toggle2FaForm;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/profile/settings/authentication', name: 'authentication_settings_')]
final class SettingController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('domain/authentication/setting/index.html.twig');
    }

    #[Route('/2fa', name: '2fa', methods: ['GET', 'POST'])]
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
                $this->addFlash("success", "Paramètres 2FA modifiés");
                return $this->redirectSeeOther('authentication_settings_2fa');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        }

        $qrcode = $authenticator->getQRContent($user);
        return $this->render(
            view: 'domain/authentication/setting/2fa.html.twig',
            parameters: [
                'qrcode_content' => $qrcode,
                'form' => $form->createView()
            ]
        );
    }
}
