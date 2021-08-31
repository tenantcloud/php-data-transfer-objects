<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * DTO which uses PascalCase for it's values' keys.
 *
 * @see DataTransferObject
 */
abstract class PascalDataTransferDTO implements Arrayable, JsonSerializable
{
	use IsDataTransferObject;

	/**
	 * {@inheritdoc}
	 */
	protected function methodToKey(string $key): string
	{
		return ucfirst($key);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function keyToMethod(string $key): string
	{
		return ucfirst($key);
	}
}
