<?php

namespace TenantCloud\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;
use Webmozart\Assert\Assert;

trait IsMigrationDataTransferObject
{
	use ForwardsCalls;

	/** @var list<mixed> */
	private static array $missingValues = [];

	/**
	 * @param list<mixed> $values
	 */
	public static function treatAsMissing(array $values): void
	{
		self::$missingValues = $values;
	}

	/**
	 * @deprecated
	 */
	public static function create(): static
	{
		return (new ReflectionClass(static::class))->newInstanceWithoutConstructor();
	}

	/**
	 * @param array|DataTransferObject $data
	 * @param array                    $map  Key-value map, where key is key from $data and value is mapped field from ->fields
	 *
	 * @return static
	 *
	 * @deprecated
	 *
	 * Create instance of self and fill it with given data
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

		return static::create()->fillOrDefaults($data);
	}

	/**
	 * @return static
	 *
	 * @deprecated
	 *
	 * Fill with given data
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
	 * @return static
	 *
	 * @deprecated
	 *
	 * Fill with given data or set defaults
	 */
	public function fillOrDefaults(array $data)
	{
		$constructorParams = (new ReflectionClass(static::class))->getConstructor()?->getParameters() ?? [];

		foreach ((new ReflectionClass(static::class))->getProperties() as $property) {
			/** @var ReflectionParameter|null $promoted */
			$promoted = $property->isPromoted() ?
				Arr::first($constructorParams, fn (ReflectionParameter $parameter) => $parameter->name === $property->name) :
				null;

			if (!$property->hasDefaultValue() && !$promoted?->isDefaultValueAvailable()) {
				continue;
			}

			$keyName = $this->propertyToKey($property->name);

			if (isset($data[$keyName])) {
				continue;
			}

			$defaultValue = $promoted ? $promoted->getDefaultValue() : $property->getDefaultValue();

			$property->setValue($this, $defaultValue);
		}

		return $this->fill($data);
	}

	/**
	 * @deprecated
	 */
	public function all(): array
	{
		return collect(get_object_vars($this))
			->mapWithKeys(fn (mixed $value, string $key) => [$this->propertyToKey($key) => $value])
			->all();
	}

	/**
	 * @deprecated
	 */
	public function toArray(): array
	{
		return array_map(static function ($value) {
			if (is_array($value)) {
				return array_map(static fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value, $value);
			}

			return $value instanceof Arrayable ? $value->toArray() : $value;
		}, $this->all());
	}

	/**
	 * @deprecated
	 */
	public function jsonSerialize(): array
	{
		return $this->all();
	}

	/**
	 * Transform data key from property name to stored key name. (FieldName (from setFieldName, getFieldName) -> field_name).
	 */
	abstract protected function propertyToKey(string $key): string;

	/**
	 * Transform data key from stored key name to property name. (field_name -> FieldName (from setFieldName, getFieldName)).
	 */
	abstract protected function keyToProperty(string $key): string;

	/**
	 * Internal method for getters.
	 */
	protected function get(string $key)
	{
		$this->assertFieldExists($key);

		return $this->{$this->keyToProperty($key)} ?? null;
	}

	/**
	 * Internal method for has* methods.
	 */
	protected function has(string $key): bool
	{
		$this->assertFieldExists($key);

		return (new ReflectionProperty(static::class, $this->keyToProperty($key)))->isInitialized($this) &&
			!in_array($this->{$this->keyToProperty($key)}, self::$missingValues, true);
	}

	/**
	 * Internal method for setters.
	 *
	 * @return static
	 */
	protected function set(string $key, $data): self
	{
		$this->assertFieldExists($key);
		(fn () => $this->{$this->keyToProperty($key)} = $data)->call($this);

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
		return property_exists($this, $this->keyToProperty($key));
	}

	protected function methodToKey(string $key): string
	{
		return $this->propertyToKey(lcfirst($key));
	}

	protected function keyToMethod(string $key): string
	{
		return ucfirst($this->keyToProperty($key));
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
}
