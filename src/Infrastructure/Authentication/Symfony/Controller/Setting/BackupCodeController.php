<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\Controller\Setting;

use Application\Authentication\Command\ExportBackupCodeCommand;
use Application\Authentication\Command\RegenerateBackupCodeCommand;
use Domain\Authentication\Entity\User;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BackupCodeSettingController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/profile/settings/authentication/backup_codes', name: 'authentication_setting_backup_codes_')]
final class BackupCodeController extends AbstractController
{
    #[Route('/regenerate', name: 'regenerate', methods: ['POST'])]
    public function regenerate(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $this->dispatchSync(new RegenerateBackupCodeCommand($user));
            $this->addFlash('success', 'Les codes ont été régénérés avec succès');
        } catch (\Throwable $e) {
            $this->handleUnexpectedException($e);
        }

        return $this->redirectSeeOther('authentication_settings_index');
    }

    #[Route('/export', name: 'export', methods: ['GET', 'POST'])]
    public function export(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $envelope = $this->dispatchSync(new ExportBackupCodeCommand($user));

            /** @var HandledStamp $stamp */
            $stamp = $envelope?->last(HandledStamp::class);

            $response = new Response(strval($stamp->getResult()));
            $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
                disposition: HeaderUtils::DISPOSITION_ATTACHMENT,
                filename: 'unh_rapports_backup_code.txt'
            ));

            return $response;
        } catch (\Throwable $e) {
            $this->handleUnexpectedException($e);
        }

        return $this->redirectSeeOther('authentication_settings_index');
    }
}
