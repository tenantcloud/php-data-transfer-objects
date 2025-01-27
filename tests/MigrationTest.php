<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Tests\Stubs\MigrationStubDTO;
use Tests\Stubs\StubDTO;
use Tests\Stubs\TestEnum;

class MigrationTest extends TestCase
{
	public function testRegularConstruction(): void
	{
		$dto = new MigrationStubDTO('test', $stub = StubDTO::create()->setName('nested'), TestEnum::$ONE);

		self::assertSame([
			'name' => 'test',
			'dto'  => $stub,
			'enum' => TestEnum::$ONE,
		], $dto->all());
		self::assertSame([
			'name' => 'test',
			'dto'  => $stub,
			'enum' => TestEnum::$ONE,
		], $dto->jsonSerialize());
		self::assertSame([
			'name' => 'test',
			'dto'  => [
				'name' => 'nested',
			],
			'enum' => TestEnum::$ONE,
		], $dto->toArray());
		self::assertSame('test', $dto->name);
		self::assertSame('test', $dto->getName());
		self::assertTrue($dto->hasName());
		$dto->setName('test2');
		self::assertSame('test2', $dto->getName());
		self::assertSame($stub, $dto->getDto());
		self::assertTrue($dto->hasDto());
		self::assertSame(TestEnum::$ONE, $dto->getEnum());
		self::assertTrue($dto->hasEnum());

		$this->expectExceptionMessage('Cannot modify readonly property Tests\\Stubs\\MigrationStubDTO::$enum');

		$dto->setEnum(TestEnum::$ONE);
	}

	public function testLegacyConstruction(): void
	{
		$dto = MigrationStubDTO::create();

		self::assertSame([], $dto->all());
		self::assertSame([], $dto->jsonSerialize());
		self::assertSame([], $dto->toArray());

		self::assertNull($dto->getName());
		self::assertFalse($dto->hasName());
		self::assertNull($dto->getDto());
		self::assertFalse($dto->hasDto());
		self::assertNull($dto->getEnum());
		self::assertFalse($dto->hasEnum());

		$dto->setName('test')
			->setDto($stub = StubDTO::create()->setName('nested'))
			->setEnum(TestEnum::$ONE);

		self::assertSame([
			'name' => 'test',
			'dto'  => $stub,
			'enum' => TestEnum::$ONE,
		], $dto->all());
		self::assertSame([
			'name' => 'test',
			'dto'  => $stub,
			'enum' => TestEnum::$ONE,
		], $dto->jsonSerialize());
		self::assertSame([
			'name' => 'test',
			'dto'  => [
				'name' => 'nested',
			],
			'enum' => TestEnum::$ONE,
		], $dto->toArray());
		self::assertSame('test', $dto->name);
		self::assertSame('test', $dto->getName());
		self::assertTrue($dto->hasName());
		$dto->setName('test2');
		self::assertSame('test2', $dto->getName());
		self::assertSame($stub, $dto->getDto());
		self::assertTrue($dto->hasDto());
		self::assertSame(TestEnum::$ONE, $dto->getEnum());
		self::assertTrue($dto->hasEnum());

		$this->expectExceptionMessage('Cannot modify readonly property Tests\\Stubs\\MigrationStubDTO::$enum');

		$dto->setEnum(TestEnum::$ONE);
	}

	public function testLegacyConstructionFromArray(): void
	{
		$dto = MigrationStubDTO::from([
			'name' => 'test',
			'dto'  => $stub = StubDTO::create()->setName('nested'),
			'enum' => TestEnum::$ONE,
		]);

		self::assertSame([
			'name' => 'test',
			'dto'  => $stub,
			'enum' => TestEnum::$ONE,
		], $dto->all());
		self::assertSame([
			'name' => 'test',
			'dto'  => $stub,
			'enum' => TestEnum::$ONE,
		], $dto->jsonSerialize());
		self::assertSame([
			'name' => 'test',
			'dto'  => [
				'name' => 'nested',
			],
			'enum' => TestEnum::$ONE,
		], $dto->toArray());
		self::assertSame('test', $dto->name);
		self::assertSame('test', $dto->getName());
		self::assertTrue($dto->hasName());
		$dto->setName('test2');
		self::assertSame('test2', $dto->getName());
		self::assertSame($stub, $dto->getDto());
		self::assertTrue($dto->hasDto());
		self::assertSame(TestEnum::$ONE, $dto->getEnum());
		self::assertTrue($dto->hasEnum());

		$this->expectExceptionMessage('Cannot modify readonly property Tests\\Stubs\\MigrationStubDTO::$enum');

		$dto->setEnum(TestEnum::$ONE);
	}
}
