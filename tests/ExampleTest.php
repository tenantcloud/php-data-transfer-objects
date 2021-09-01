<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use TenantCloud\DataTransferObjects\ExampleDTO;

class ExampleTest extends TestCase
{
	public function testExample(): void
	{
		self::assertNotNull(ExampleDTO::create());
	}
}
