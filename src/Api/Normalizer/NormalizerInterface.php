<?php

namespace App\Api\Normalizer;

interface NormalizerInterface
{
	public function normalize($value) : mixed;
}