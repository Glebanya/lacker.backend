<?php


namespace App\Utils\Authentication;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

final class ApplicationAuthenticator extends AbstractGuardAuthenticator
{

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse(['message' => 'Authentication Required'], Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request): bool
    {
        return true;
    }

    public function getCredentials(Request $request)
    {
        return $request->get('app_id') ?? $request->request->get('app_id');
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        // TODO: Implement getUser() method.
    }
}