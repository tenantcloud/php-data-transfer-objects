<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use TenantCloud\DataTransferObjects\ExampleDTO;
use TenantCloud\Standard\Enum\ValueEnum;
use Tests\stubs\TestEnum;

class ExampleTest extends TestCase
{
	public function testExample(): void
	{
		self::assertNotNull(ExampleDTO::create());
	}

	public function testSerializeWithDTO(): void
	{
		$dto = ExampleDTO::from([
			'enum' => TestEnum::$ONE,
		]);

		$serialized = serialize($dto);

		/** @var ExampleDTO $unserialized */
		$unserialized = unserialize($serialized);

		self::assertInstanceOf(ValueEnum::class, $unserialized->getEnum());
		self::assertEquals(1, $unserialized->getEnum()->value());
	}
}
