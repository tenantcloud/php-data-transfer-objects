<?php

namespace Tests\Stubs;

use TenantCloud\DataTransferObjects\MigrationDataTransferObject;

class MigrationStubDTO extends MigrationDataTransferObject
{
	public function __construct(
		public string $name,
		public readonly StubDTO $dto,
		public readonly TestEnum $enum,
		public ?string $withDefault = null,
	) {}
}
