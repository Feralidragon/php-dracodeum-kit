<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structures;

use Dracodeum\Kit\Structure;
use Dracodeum\Kit\Interfaces\Log\Event\Data as ILogEventData;
use Dracodeum\Kit\Structures\Uid\Exceptions;
use Dracodeum\Kit\Root\Log;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This structure represents the UID (unique identifier) of a resource.
 * 
 * @property int|string|null $id [default = null]
 * <p>The ID.</p>
 * @property string|null $name [default = null]
 * <p>The name.</p>
 * @property string|null $scope [default = auto]
 * <p>The scope.</p>
 * @property string|null $base_scope [default = null]
 * <p>The base scope, optionally set with placeholders as <samp>{{placeholder}}</samp>, 
 * corresponding directly to given scope IDs.<br>
 * <br>
 * If set, then placeholders must be exclusively composed of identifiers, 
 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).</p>
 * @see https://en.wikipedia.org/wiki/Identifier
 */
class Uid extends Structure implements ILogEventData
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('id')
			->addEvaluator(function (&$value): bool {
				return self::evaluateId($value, true);
			})
			->setDefaultValue(null)
		;
		$this->addProperty('name')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('scope')
			->setAsString(true, true)
			->setDefaultGetter(function () {
				$base_scope = $this->base_scope;
				$scope_ids = $this->scope_ids;
				if ($base_scope === null) {
					return null;
				} elseif (!count($scope_ids)) {
					return $base_scope;
				}
				return UText::fill($base_scope, $scope_ids);
			})
		;
		$this->addProperty('base_scope')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('scope_ids')
			->setAsArray(function (&$key, &$value): bool {
				return self::evaluateScopeId($key, $value);
			})
			->setDefaultValue([])
		;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Interfaces\Log\Event\Data)
	/** {@inheritdoc} */
	public function getLogEventData()
	{
		//initialize
		$strings = [];
		
		//name
		$name = $this->name;
		if ($name !== null) {
			$strings[] = $name;
		}
		
		//scope
		$scope = $this->scope;
		if ($scope !== null) {
			$strings[] = $scope;
		}
		
		//id
		$id = $this->id;
		if ($id !== null) {
			$strings[] = $id;
		}
		
		//return
		if (count($strings)) {
			array_unshift($strings, 'UID');
			return Log::composeEventTag($strings);
		}
		return null;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\IntegerPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractIntegerProperties(int $integer): ?array
	{
		return ['id' => $integer];
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\StringPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractStringProperties(string $string): ?array
	{
		return ['id' => $string];
	}
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value as an ID.
	 * 
	 * Only the following types and formats can be evaluated into an ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an ID.</p>
	 */
	final public static function evaluateId(&$value, bool $nullable = false): bool
	{
		return self::processIdCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an ID.
	 * 
	 * Only the following types and formats can be coerced into an ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Structures\Uid\Exceptions\IdCoercionFailed
	 * @return int|string|null
	 * <p>The given value coerced into an ID.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceId($value, bool $nullable = false)
	{
		self::processIdCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an ID.
	 * 
	 * Only the following types and formats can be coerced into an ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Structures\Uid\Exceptions\IdCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an ID.</p>
	 */
	final public static function processIdCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//check
		if (is_int($value) || is_string($value)) {
			return true;
		}
		
		//nullable
		if ($value === null) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\IdCoercionFailed([
				'value' => $value,
				'uid' => static::class,
				'error_code' => Exceptions\IdCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$id = $value;
		if (UType::evaluateInteger($id) || UType::evaluateString($id)) {
			$value = $id;
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\IdCoercionFailed([
			'value' => $value,
			'uid' => static::class,
			'error_code' => Exceptions\IdCoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only the following types and formats can be coerced into an ID:\n" . 
				" - an integer or string;\n" . 
				" - an object implementing the \"__toString\" method;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Stringifiable\" interface."
		]);
	}
	
	/**
	 * Evaluate a given value with a given name as a scope ID.
	 * 
	 * Only the following types and formats can be evaluated into a scope ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param string $name
	 * <p>The name to evaluate with.</p>
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value with the given name was successfully evaluated into a 
	 * scope ID.</p>
	 */
	final public static function evaluateScopeId(string $name, &$value, bool $nullable = false): bool
	{
		return self::processScopeIdCoercion($name, $value, $nullable, true);
	}
	
	/**
	 * Coerce a given value with a given name into a scope ID.
	 * 
	 * Only the following types and formats can be coerced into a scope ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param string $name
	 * <p>The name to coerce with.</p>
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Structures\Uid\Exceptions\ScopeIdCoercionFailed
	 * @return int|string|null
	 * <p>The given value with the given name coerced into a scope ID.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceScopeId(string $name, $value, bool $nullable = false)
	{
		self::processScopeIdCoercion($name, $value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value with a given name into a scope ID.
	 * 
	 * Only the following types and formats can be coerced into a scope ID:<br>
	 * &nbsp; &#8226; &nbsp; an integer or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param string $name
	 * <p>The name to process with.</p>
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Structures\Uid\Exceptions\ScopeIdCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value with the given name was successfully coerced into a scope ID.</p>
	 */
	final public static function processScopeIdCoercion(
		string $name, &$value, bool $nullable = false, bool $no_throw = false
	): bool
	{
		try {
			if (!self::processIdCoercion($value, $nullable, $no_throw)) {
				return false;
			}
		} catch (Exceptions\IdCoercionFailed $exception) {
			throw new Exceptions\ScopeIdCoercionFailed([
				'name' => $name,
				'value' => $exception->value,
				'uid' => static::class,
				'error_code' => $exception->error_code,
				'error_message' => $exception->error_message
			]);
		}
		return true;
	}
	
	/**
	 * Evaluate a given set of values as a set of scope IDs.
	 * 
	 * Only the following types and formats can be evaluated into scope IDs:<br>
	 * &nbsp; &#8226; &nbsp; an array of integers or strings;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> 
	 * interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param array $values [reference]
	 * <p>The set of values to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given set of values was successfully evaluated into a set of scope IDs.</p>
	 */
	final public static function evaluateScopeIds(array &$values): bool
	{
		return self::processScopeIdsCoercion($values, true);
	}
	
	/**
	 * Coerce a given set of values into a set of scope IDs.
	 * 
	 * Only the following types and formats can be coerced into scope IDs:<br>
	 * &nbsp; &#8226; &nbsp; an array of integers or strings;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> 
	 * interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param array $values
	 * <p>The set of values to coerce (validate and sanitize).</p>
	 * @throws \Dracodeum\Kit\Structures\Uid\Exceptions\ScopeIdCoercionFailed
	 * @return int[]|string[]
	 * <p>The given set of values coerced into a set of scope IDs.</p>
	 */
	final public static function coerceScopeIds(array $values): array
	{
		self::processScopeIdsCoercion($values);
		return $values;
	}
	
	/**
	 * Process the coercion of a given set of values into a set of scope IDs.
	 * 
	 * Only the following types and formats can be coerced into scope IDs:<br>
	 * &nbsp; &#8226; &nbsp; an array of integers or strings;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an array of objects implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> 
	 * interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param array $values [reference]
	 * <p>The set of values to process (validate and sanitize).</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Structures\Uid\Exceptions\ScopeIdCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given set of values was successfully coerced into a set of scope IDs.</p>
	 */
	final public static function processScopeIdsCoercion(array &$values, bool $no_throw = false): bool
	{
		$ids = [];
		foreach ($values as $name => $value) {
			if (self::processScopeIdCoercion($name, $value, false, $no_throw)) {
				$ids[$name] = $value;
			} else {
				return false;
			}
		}
		$values = $ids;
		return true;
	}
}
