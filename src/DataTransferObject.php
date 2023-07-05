<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * A base DTO. Basically, this is a replacement for key-value arrays. An object that just holds specific data.
 *
 * Examples:
 *  - {@see ExampleDTO} - example of common DTO object.
 *
 * Features:
 *  - getters, setters and other helper functions (with ability to add your own)
 *  - only allowing to fill keys you specified, ignore others
 *  - type-checking across the project (instead of arrays that accept god knows what)
 *  - custom getters, setters, casting, asserts and other features of object
 *
 * How-to-use:
 *  - create a class extending this one
 *  - fill out ->fields with snake_case keys that can be filled
 *  - add {@method} annotation for get, set and has methods for all of the fields
 *  - optionally write your own getters or setters when you want custom functionality
 */
abstract class DataTransferObject implements Arrayable, JsonSerializable
{
	use IsDataTransferObject;

	protected function methodToKey(string $key): string
	{
		return Str::snake($key);
	}

	protected function keyToMethod(string $key): string
	{
		return Str::studly($key);
	}
}
