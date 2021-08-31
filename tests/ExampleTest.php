<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use TenantCloud\DataTransferObjects\ExampleDTO;

class ExampleTest extends TestCase
{
	public function testExample()
	{
		self::assertNotNull(ExampleDTO::create());
	}
}
