<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Structures;

use Dracodeum\Kit\Structure;
use Dracodeum\Kit\Components\Store\Structures\Uid\Exceptions;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This structure represents the UID (unique identifier) of a resource in a store.
 * 
 * @property int|float|string|null $id [coercive] [default = null]
 * <p>The ID.</p>
 * @property string|null $name [coercive] [default = null]
 * <p>The name.<br>
 * If set, then it cannot be empty.</p>
 * @property string|null $scope [default = auto]
 * <p>The scope.<br>
 * If set, then it cannot be empty.</p>
 * @property string|null $base_scope [coercive] [default = null]
 * <p>The base scope, optionally set with placeholders as <samp>{{placeholder}}</samp>, 
 * corresponding directly to given scope values.<br>
 * <br>
 * If set, then it cannot be empty, and placeholders must be exclusively composed by identifiers, 
 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
 * <br>
 * They may also be used with pointers to specific object properties or associative array values, 
 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
 * with no limit on the number of chained pointers.<br>
 * <br>
 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
 * @property array $scope_values [coercive] [default = []]
 * <p>The scope values, as <samp>name => value</samp> pairs.</p>
 * @see https://en.wikipedia.org/wiki/Identifier
 */
class Uid extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('id')
			->addEvaluator(function (&$value): bool {
				return static::evaluateId($value, true);
			})
			->setDefaultValue(null)
		;
		$this->addProperty('name')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('scope')
			->setAsString(true, true)
			->setDefaultGetter(function () {
				$base_scope = $this->get('base_scope');
				$scope_values = $this->get('scope_values');
				if ($base_scope === null) {
					return null;
				} elseif (empty($scope_values)) {
					return $base_scope;
				}
				return UText::fill($base_scope, $scope_values);
			})
		;
		$this->addProperty('base_scope')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('scope_values')->setAsArray()->setDefaultValue([]);
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\IntegerPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractIntegerProperties(int $integer): ?array
	{
		return ['id' => $integer];
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\FloatPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractFloatProperties(float $float): ?array
	{
		return ['id' => $float];
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
	 * &nbsp; &#8226; &nbsp; an integer, float or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
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
	 * &nbsp; &#8226; &nbsp; an integer, float or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Structures\Uid\Exceptions\IdCoercionFailed
	 * @return int|float|string|null
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
	 * &nbsp; &#8226; &nbsp; an integer, float or string;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Components\Store\Structures\Uid\Exceptions\IdCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an ID.</p>
	 */
	final public static function processIdCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//check
		if (is_int($value) || is_float($value) || is_string($value)) {
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
		if (UType::evaluateNumber($id) || UType::evaluateString($id)) {
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
				" - an integer, float or string;\n" . 
				" - an object implementing the \"__toString\" method;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Stringifiable\" interface."
		]);
	}
}
