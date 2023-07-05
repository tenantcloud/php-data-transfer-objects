<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

/**
 * DTO which uses camelCase for it's values' keys.
 *
 * @see DataTransferObject
 */
abstract class CamelDataTransferObject implements Arrayable
{
	use IsDataTransferObject;

	protected function methodToKey(string $key): string
	{
		return lcfirst($key);
	}

	protected function keyToMethod(string $key): string
	{
		return ucfirst($key);
	}
}
