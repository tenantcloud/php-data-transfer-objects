<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use TenantCloud\Standard\Enum\ValueEnum;
use Webmozart\Assert\Assert;

/**
 * Shared DTO object logic.
 */
trait IsDataTransferObject
{
	use ForwardsCalls;

	protected array $fields = [];

	/** @var array<string, class-string<ValueEnum>> */
	protected array $enums = [];

	private array $data = [];

	/**
	 * @return static
	 */
	public static function create()
	{
		return new static();
	}

	/**
	 * Create instance of self and fill it with given data.
	 *
	 * @param array|DataTransferObject $data
	 *
	 * @return static
	 */
	public static function from($data, array $map = [])
	{
		if ($data instanceof static) {
			return clone $data;
		}

		// Replace old keys with new ones.
		foreach ($map as $oldKey => $newKey) {
			if (Arr::has($data, $oldKey)) {
				Arr::set($data, $newKey, Arr::pull($data, $oldKey));
			}
		}

		return static::create()->fill($data);
	}

	/**
	 * Fill with given data.
	 *
	 * @return static
	 */
	public function fill(array $data)
	{
		foreach ($data as $key => $value) {
			if (!$this->doesFieldExist($key)) {
				continue;
			}

			$this->{'set' . $this->keyToMethod($key)}($value);
		}

		return $this;
	}

	/**
	 * Return all data.
	 */
	public function all(): array
	{
		return $this->data;
	}

	public function toArray(): array
	{
		return array_map(static function ($value) {
			if (is_array($value)) {
				return array_map(static fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value, $value);
			}

			return $value instanceof Arrayable ? $value->toArray() : $value;
		}, $this->data);
	}

	public function jsonSerialize(): array
	{
		return $this->all();
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->data);
	}

	/**
	 * @param DataTransferObject $dataTransferObject
	 *
	 * @return static
	 */
	public function diff(self $dataTransferObject): self
	{
		$different = array_diff_assoc($this->all(), $dataTransferObject->all());

		return (clone $this)->only(array_keys($different));
	}

	/**
	 * @return static
	 */
	public function only(array $keys): self
	{
		return static::from(Arr::only($this->data, $keys));
	}

	/**
	 * Transform data key from method name to stored key name. (FieldName (from setFieldName, getFieldName) -> field_name).
	 */
	abstract protected function methodToKey(string $key): string;

	/**
	 * Transform data key from stored key name to method name. (field_name -> FieldName (from setFieldName, getFieldName)).
	 */
	abstract protected function keyToMethod(string $key): string;

	/**
	 * Internal method for getters.
	 */
	protected function get(string $key)
	{
		$this->assertFieldExists($key);

		return Arr::get($this->data, $key);
	}

	/**
	 * Internal method for has* methods.
	 */
	protected function has(string $key): bool
	{
		$this->assertFieldExists($key);

		return Arr::has($this->data, $key);
	}

	/**
	 * Internal method for setters.
	 *
	 * @return static
	 */
	protected function set(string $key, $data): self
	{
		$this->assertFieldExists($key);

		Arr::set($this->data, $key, $data);

		return $this;
	}

	/**
	 * Asserts that a given field key is allowed to exist.
	 */
	protected function assertFieldExists(string $key): void
	{
		Assert::true($this->doesFieldExist($key), "Key {$key} doesn't exist");
	}

	/**
	 * Whether given field can be filled.
	 */
	protected function doesFieldExist(string $key): bool
	{
		return in_array($key, $this->fields, true);
	}

	/**
	 * Forwards ->getFieldName(), ->setFieldName($value) and ->hasFieldName() to reduce boilerplate.
	 */
	public function __call($method, $arguments)
	{
		if (Str::startsWith($method, 'set')) {
			return $this->set($this->methodToKey(mb_substr($method, 3)), ...$arguments);
		}

		if (Str::startsWith($method, 'get')) {
			return $this->get($this->methodToKey(mb_substr($method, 3)));
		}

		if (Str::startsWith($method, 'has')) {
			return $this->has($this->methodToKey(mb_substr($method, 3)));
		}

		static::throwBadMethodCallException($method);
	}

	public function __serialize(): array
	{
		$data = $this->all();

		foreach ($this->enums as $key => $item) {
			$enum = Arr::get($data, $key);

			if ($enum instanceof ValueEnum) {
				Arr::set($data, $key, $enum->value());
			}

			if (is_iterable($enum)) {
				foreach ($enum as $index => $enumItem) {
					if ($enumItem instanceof ValueEnum) {
						Arr::set($data[$key], $index, $enumItem->value());
					}
				}
			}
		}

		foreach ($data as $index => $dataItem) {
			$data[$index] = serialize($dataItem);
		}

		return [
			'fields' => $this->fields,
			'enums'  => $this->enums,
			'data'   => $data,
		];
	}

	public function __unserialize(array $data): void
	{
		$this->fields = $data['fields'];
		$this->enums = $data['enums'];

		$dataItems = $data['data'];

		foreach ($dataItems as $index => $dataItem) {
			$dataItems[$index] = unserialize($dataItem);
		}

		foreach ($this->enums as $index => $enum) {
			if (Arr::has($dataItems, $index)) {
				$serializedItem = Arr::get($dataItems, $index);

				if (is_iterable($serializedItem)) {
					foreach ($serializedItem as $key => $item) {
						Arr::set($dataItems[$index], $key, $item === null ? $item : $enum::fromValue($item));
					}
				} else {
					/* @var ValueEnum|null $enum */
					Arr::set($dataItems, $index, $serializedItem === null ? $serializedItem : $enum::fromValue($serializedItem));
				}
			}
		}

		$this->data = $dataItems;
	}
}
