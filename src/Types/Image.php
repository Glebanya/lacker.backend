<?php

namespace App\Types;

use App\Utils\Environment;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Image implements \JsonSerializable
{
	#[Assert\Url]
	protected string|null $url;

	public function __construct(string $file)
	{
		if (is_string($file))
		{
			$this->url = $file;
		}
		else
		{
			$this->url = null;
		}
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function jsonSerialize()
	{
		return $this->getUrl();
	}
}