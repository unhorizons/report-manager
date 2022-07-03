<?php

declare(strict_types=1);

namespace Infrastructure\Administration\Symfony\Controller;

use Application\Authentication\Command\DeleteUserCommand;
use Application\Authentication\Command\RegisterUserCommand;
use Application\Authentication\Command\UpdateUserCommand;
use Domain\Authentication\Entity\User;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Infrastructure\Authentication\Symfony\Form\RegisterUserForm;
use Infrastructure\Authentication\Symfony\Form\UpdateUserForm;
use Infrastructure\Shared\Symfony\Controller\AbstractController;
use Infrastructure\Shared\Symfony\Controller\DeleteCsrfTrait;
use Infrastructure\Shared\Symfony\Controller\PaginationAssertionTrait;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class UserController.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/authentication/user', name: 'administration_user_')]
#[AsController]
final class UserController extends AbstractController
{
    use DeleteCsrfTrait;
    use PaginationAssertionTrait;

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, UserRepositoryInterface $repository): Response
    {
        $page = $request->query->getInt('page', 1);
        $this->assertIsGreaterThanZero($page);

        $data = $paginator->paginate($repository->findAll(), $page, 15);
        $this->assertNonEmptyData($page, $data);

        return $this->render(
            view: 'domain/administration/user/index.html.twig',
            parameters: [
                'data' => $data,
            ]
        );
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $command = new RegisterUserCommand();
        $form = $this->createForm(RegisterUserForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'authentication.flashes.user_registered_successfully',
                    parameters: [
                        '%name%' => $command->username
                    ],
                    domain: 'authentication'
                ));

                return $this->redirectSeeOther('administration_user_index');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/administration/user/new.html.twig',
            parameters: [
                'form' => $form->createView(),
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            view: 'domain/administration/user/show.html.twig',
            parameters: [
                'data' => $user,
            ]
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isDeleteCsrfTokenValid((string) $user->getId(), $request)) {
            try {
                $this->dispatchSync(new DeleteUserCommand($user));
                $this->addFlash('success', $this->translator->trans(
                    id: 'authentication.flashes.user_deleted_successfully',
                    parameters: [
                        '%name%' => $user->getUsername(),
                    ],
                    domain: 'authentication'
                ));

                return $this->redirectSeeOther('administration_user_index');
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
            }
        } else {
            $this->addSomethingWentWrongFlash();
        }

        return $this->redirectSeeOther('administration_user_show', [
            'id' => $user->getId(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $command = new UpdateUserCommand($user);
        $form = $this->createForm(UpdateUserForm::class, $command)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchSync($command);
                $this->addFlash('success', $this->translator->trans(
                    id: 'authentication.flashes.user_updated_successfully',
                    parameters: [
                        '%name%' => $user->getId(),
                    ],
                    domain: 'authentication'
                ));

                return $this->redirectSeeOther('administration_user_show', [
                    'id' => $user->getId(),
                ]);
            } catch (\Throwable $e) {
                $this->handleUnexpectedException($e);
                $response = new Response(status: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return $this->render(
            view: 'domain/administration/user/edit.html.twig',
            parameters: [
                'form' => $form->createView(),
                'data' => $user
            ],
            response: $this->getResponseBasedOnFormValidationStatus($form, $response ?? null)
        );
    }
}
