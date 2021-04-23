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

final class UserTokenAuthenticator extends AbstractGuardAuthenticator
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
		return $request->query->get('access_token');
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
		$jwtObject = new JWTObject($credentials);
		if ($id = $jwtObject->getUserId())
		{
			return $userProvider->loadUserByUsername($id);
		}
		return null;
	}
}