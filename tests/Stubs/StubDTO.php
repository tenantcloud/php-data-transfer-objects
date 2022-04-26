<?php

namespace Tests\Stubs;

use TenantCloud\DataTransferObjects\DataTransferObject;
use TenantCloud\DataTransferObjects\ExampleDTO;
use TenantCloud\Standard\Enum\ValueEnum;

/**
 * @method bool       hasDto()
 * @method self       setDto(ExampleDTO $dto)
 * @method ExampleDTO getDto()
 * @method bool       hasEnum()
 * @method self       setEnum(ValueEnum $enum)
 * @method ValueEnum  getEnum()
 * @method bool       hasName()
 * @method self       setName(string $name)
 * @method string     getName()
 */
class StubDTO extends DataTransferObject
{
	protected array $enums = [
		'enum' => TestEnum::class,
	];

	protected array $fields = [
		'name',
		'dto',
		'enum',
	];
}
