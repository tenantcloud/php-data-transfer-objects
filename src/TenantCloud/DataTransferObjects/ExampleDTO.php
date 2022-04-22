<?php

namespace TenantCloud\DataTransferObjects;

use TenantCloud\Standard\Enum\ValueEnum;
use Tests\Stubs\TestEnum;

/**
 * @method bool      hasFoo()
 * @method self      setFoo($foo)
 * @method mixed     getFoo()
 * @method bool      hasEnum()
 * @method self      setEnum(ValueEnum $enum)
 * @method ValueEnum getEnum()
 */
class ExampleDTO extends DataTransferObject
{
	protected array $enums = [
		'enum' => TestEnum::class,
	];

	protected array $fields = [
		'foo',
		'enum',
	];
}
