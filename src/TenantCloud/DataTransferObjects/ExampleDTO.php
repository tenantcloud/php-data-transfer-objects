<?php

namespace TenantCloud\DataTransferObjects;

/**
 * @method bool  hasFoo()
 * @method self  setFoo($foo)
 * @method mixed getFoo()
 */
class ExampleDTO extends DataTransferObject
{
	protected array $fields = [
		'foo',
	];
}
