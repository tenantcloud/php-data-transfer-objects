<?php

namespace Tests;

use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use TenantCloud\DataTransferObjects\ExampleDTO;
use TenantCloud\Standard\Enum\ValueEnum;
use Tests\Stubs\TestEnum;

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

	public function testArrayEnum(): void
	{
		$dto = ExampleDTO::from([
			'array_enum' => [TestEnum::$ONE],
		]);

		$serialized = serialize($dto);

		/** @var ExampleDTO $unserialized */
		$unserialized = unserialize($serialized);

		self::assertIsArray($unserialized->getArrayEnum());
		self::assertInstanceOf(ValueEnum::class, $unserialized->getArrayEnum()[0]);
		self::assertEquals(1, $unserialized->getArrayEnum()[0]->value());
	}

	public function testArrayEnumWithCollection(): void
	{
		$dto = ExampleDTO::from([
			'array_enum' => collect([TestEnum::$ONE]),
		]);

		$serialized = serialize($dto);

		/** @var ExampleDTO $unserialized */
		$unserialized = unserialize($serialized);

		self::assertInstanceOf(Collection::class, $unserialized->getArrayEnum());
		self::assertInstanceOf(ValueEnum::class, $unserialized->getArrayEnum()[0]);
		self::assertEquals(1, $unserialized->getArrayEnum()[0]->value());
	}

	public function testWithStringInsteadOfEnum(): void
	{
		$dto = ExampleDTO::from([
			'enum'       => TestEnum::$ONE->value(),
			'array_enum' => [TestEnum::$ONE->value()],
		]);

		$serialized = serialize($dto);

		/** @var ExampleDTO $unserialized */
		$unserialized = unserialize($serialized);

		self::assertIsArray($unserialized->getArrayEnum());
		self::assertInstanceOf(ValueEnum::class, $unserialized->getArrayEnum()[0]);
		self::assertEquals(1, $unserialized->getArrayEnum()[0]->value());
		self::assertInstanceOf(ValueEnum::class, $unserialized->getEnum());
		self::assertEquals(1, $unserialized->getEnum()->value());
	}
}
