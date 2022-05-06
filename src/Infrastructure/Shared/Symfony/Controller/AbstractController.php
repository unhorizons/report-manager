<?php

declare(strict_types=1);

namespace Infrastructure\Shared\Symfony\Controller;

use Domain\Shared\Exception\SafeMessageException;
use Infrastructure\Shared\Symfony\Messenger\DispatchTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
abstract class AbstractController extends SymfonyAbstractController
{
    use DispatchTrait;

    public function __construct(
        protected readonly MessageBusInterface $commandBus,
        protected readonly TranslatorInterface $translator,
        protected readonly LoggerInterface $logger
    ) {
    }

    protected function redirectSeeOther(string $route, array $params = []): RedirectResponse
    {
        return $this->redirectToRoute($route, $params, Response::HTTP_SEE_OTHER);
    }

    protected function getResponseBasedOnFormValidationStatus(FormInterface $form, ?Response $response = null): Response
    {
        if (null === $response) {
            $response = new Response();
        }

        if (Response::HTTP_OK === $response->getStatusCode() && $form->isSubmitted() && ! $form->isValid()) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $response;
    }

    protected function flashFormErrors(FormInterface $form): void
    {
        $errors = $this->getFormErrors($form);
        $errors = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($errors)));
        $this->addFlash(
            type: 'error',
            message: implode(separator: '\n', array: $errors)
        );
    }

    protected function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                $childErrors = $this->getFormErrors($childForm);
                if ($childErrors) {
                    $errors[] = $childErrors;
                }
            }
        }

        return $errors;
    }

    protected function handleUnexpectedException(\Throwable $e): void
    {
        if ($e instanceof SafeMessageException) {
            $message = $this->getSafeMessageException($e);
        } else {
            $message = $this->translator->trans(
                id: 'flashes.something_went_wrong',
                parameters: [],
                domain: 'messages'
            );
        }

        $this->logger->error($e->getMessage(), $e->getTrace());
        $this->addFlash('error', $message);
    }

    protected function getSafeMessageException(SafeMessageException $e): string
    {
        return $this->translator->trans(
            id: $e->getMessageKey(),
            parameters: $e->getMessageData(),
            domain: $e->getMessageDomain()
        );
    }

    protected function addSomethingWentWrongFlash(): void
    {
        $this->addFlash('error', $this->translator->trans(
            id: 'flashes.something_went_wrong',
            parameters: [],
            domain: 'messages'
        ));
    }
}
