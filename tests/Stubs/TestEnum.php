<?php

namespace Tests\Stubs;

use TenantCloud\Standard\Enum\ValueEnum;

class TestEnum extends ValueEnum
{
	public static TestEnum $ONE;

	protected static function initializeInstances(): void
	{
		self::$ONE = new self(1);
	}
}
