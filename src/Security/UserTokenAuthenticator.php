<?php


namespace App\Security;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class UserTokenAuthenticator extends AbstractGuardAuthenticator
{
	public function start(Request $request, AuthenticationException $authException = null): Response
	{
		return new JsonResponse([
			'message' => 'Authentication Required',
			'code' => $authException?->getCode() ?? 0
		], Response::HTTP_UNAUTHORIZED);
	}

	public function supports(Request $request): bool
	{
		return !preg_match('/^\/public\/auth\/(google|staff){1}$/',$request->getPathInfo()) && $request->headers->has('Authorization');
	}

	public function getCredentials(Request $request)
	{
		return $request->headers->get('Authorization');
	}

	public function checkCredentials($credentials, UserInterface $user): bool
	{
		return true;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		return new JsonResponse(
			[
				'error' => [
					'message' => $exception->getMessageKey(),
					'code' => $exception->getCode()
				]
			],
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
		if ($id = (new JWTObject($credentials))->getUserId())
		{
			return $userProvider->loadUserByUsername($id);
		}
		return null;
	}
}