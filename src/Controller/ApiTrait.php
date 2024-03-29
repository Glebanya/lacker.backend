<?php

namespace App\Controller;

use App\Api\ApiEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ApiTrait
{
	protected function getFieldsRequest(): string
	{
		return 'fields';
	}
	protected function getFieldsSeparator() : string
	{
		return ',';
	}

	protected function getRequestedFields(Request $request): array|null
	{
		if ($raw = $request->query->get($this->getFieldsRequest(), default: null))
		{
			return array_filter(
				array_map('trim', explode($this->getFieldsSeparator(), $raw)),
				function($fields) : bool {
					return !empty($fields);
				}
			);
		}
		return null;
	}


	protected function getContent(Request $request): ?array
	{
		if (!$data = json_decode($request->getContent() , true))
		{
			if (json_last_error() !== JSON_ERROR_NONE)
			{
				throw new BadRequestHttpException('invalid json body: '.json_last_error_msg());
			}
		}

		return $data ?? [];
	}
}