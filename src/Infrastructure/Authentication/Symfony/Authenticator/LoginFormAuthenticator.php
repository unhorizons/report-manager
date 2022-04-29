<?php

declare(strict_types=1);

namespace Infrastructure\Authentication\Symfony\Authenticator;

use Domain\Authentication\Repository\UserRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'authentication_login';
    private ?UserInterface $user = null;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $identifier = (string) $request->request->get('identifier', '');
        $request->getSession()->set(Security::LAST_USERNAME, $identifier);

        $passport = new Passport(
            userBadge: new UserBadge(
                userIdentifier: $identifier,
                userLoader: fn (string $identifier) => $this->repository->findOneByEmailOrUsername($identifier)
            ),
            credentials: new PasswordCredentials((string) $request->request->get('password', '')),
            badges: [
                new CsrfTokenBadge(csrfTokenId: 'authenticate', csrfToken: strval($request->get('_token', ''))),
                new RememberMeBadge(),
            ]
        );

        $this->user = $this->createToken($passport, 'main')->getUser();

        return $passport;
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): RedirectResponse {
        $redirect = strval($request->get('_redirect'));
        if ($redirect) {
            return new RedirectResponse($redirect);
        }

        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
