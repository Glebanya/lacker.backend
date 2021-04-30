<?php

namespace App\Types;

use App\Utils\Environment;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image implements \JsonSerializable
{
	protected string|null $url;

	public function __construct(UploadedFile|string $file)
	{
		if (is_string($file))
		{
			$this->url = $file;
		}
		elseif ($file instanceof UploadedFile)
		{
			try
			{
				$this->url = $file->move(Environment::get('uploads'), $fileName = uniqid().'.'.$file->guessExtension())->getPath();
			}
			catch (FileException $exception) {}
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