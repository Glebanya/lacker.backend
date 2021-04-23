<?php

namespace App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Psr\Container\ContainerInterface as PsrContainerInterface;

final class ContainerFacade implements PsrContainerInterface
{
	private static ContainerFacade|null $instance = null;

	/**
	 * @param ContainerInterface $container
	 */
	private function __construct(private ContainerInterface $container)
	{}

	public function has(string $id): bool
	{
		return $this->container->has($id);
	}

	public function get(string $id)
	{
		return $this->container->get($id);
	}

	public static function init(ContainerInterface $container): ContainerFacade|null
	{
		if (null === ContainerFacade::$instance)
		{
			ContainerFacade::$instance = new ContainerFacade($container);
		}

		return ContainerFacade::$instance;
	}

	public static function instance() : ContainerFacade
	{
		return ContainerFacade::$instance;
	}
}