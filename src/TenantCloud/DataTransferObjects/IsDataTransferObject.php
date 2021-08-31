<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Webmozart\Assert\Assert;

/**
 * Shared DTO object logic.
 */
trait IsDataTransferObject
{
	use ForwardsCalls;

	protected array $fields = [];

	private array $data = [];

	/**
	 * Forwards ->getFieldName(), ->setFieldName($value) and ->hasFieldName() to reduce boilerplate.
	 *
	 * @param mixed $method
	 * @param mixed $arguments
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

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new static();
	}

	/**
	 * Create instance of self and fill it with given data.
	 *
	 * @param array|DataTransferObject $data
	 * @param array                    $map  Key-value map, where key is key from $data and value is mapped field from ->fields
	 *
	 * @return static
	 */
	public static function from($data, array $map = []): self
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
	public function fill(array $data): self
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

	/**
	 * {@inheritdoc}
	 */
	public function toArray(): array
	{
		return array_map(static function ($value) {
			if (is_array($value)) {
				return array_map(static fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value, $value);
			}

			return $value instanceof Arrayable ? $value->toArray() : $value;
		}, $this->data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize(): array
	{
		return $this->all();
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	public function isEmpty(): bool
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
	 *
	 * @return mixed
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
	 * @param $data
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
}
