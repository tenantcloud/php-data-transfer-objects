<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class MigrationDataTransferObject implements Arrayable, JsonSerializable
{
	use IsMigrationDataTransferObject;

	protected function propertyToKey(string $key): string
	{
		return Str::snake($key);
	}

	protected function keyToProperty(string $key): string
	{
		return lcfirst(Str::studly($key));
	}
}
