<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Structures;

use Dracodeum\Kit\Structure;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This structure represents the UID (unique identifier) of a resource in a store.
 * 
 * @property mixed $value [default = null]
 * <p>The value.</p>
 * @property string|null $name [coercive] [default = null]
 * <p>The name.<br>
 * If set, then it cannot be empty.</p>
 * @property-read string|null $scope [readonly] [default = auto]
 * <p>The scope.</p>
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
 * @property callable|null $scope_value_stringifier [coercive] [default = null]
 * <p>The function to use to stringify a given scope value for a given placeholder.<br>
 * It is expected to be compatible with the following signature:<br>
 * <br>
 * <code>function (string $placeholder, $value): ?string</code><br>
 * <br>
 * Parameters:<br>
 * &nbsp; &#8226; &nbsp; <code><b>string $placeholder</b></code><br>
 * &nbsp; &nbsp; &nbsp; The placeholder to stringify for.<br>
 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
 * &nbsp; &nbsp; &nbsp; The value to stringify.<br>
 * <br>
 * Return: <code><b>string|null</b></code><br>
 * The stringified scope value for the given placeholder or <code>null</code> if no stringification occurred.</p>
 */
class Uid extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('value')->setDefaultValue(null);
		$this->addProperty('name')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('scope')
			->setMode('r')
			->setGetter(function () {
				//initialize
				$base_scope = $this->get('base_scope');
				$scope_values = $this->get('scope_values');
				
				//check
				if ($base_scope === null) {
					return null;
				} elseif (empty($scope_values)) {
					return $base_scope;
				}
				
				//return
				return UText::fill($base_scope, $scope_values, null, [
					'stringifier' => $this->get('scope_value_stringifier')
				]);
			})
		;
		$this->addProperty('base_scope')->setAsString(true, true)->setDefaultValue(null);
		$this->addProperty('scope_values')->setAsArray()->setDefaultValue([]);
		$this->addProperty('scope_value_stringifier')
			->setAsCallable(function (string $placeholder, $value): ?string {}, true, true)
			->setDefaultValue(null)
		;
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\IntegerPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractIntegerProperties(int $integer): ?array
	{
		return [
			'value' => $integer
		];
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\FloatPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractFloatProperties(float $float): ?array
	{
		return [
			'value' => $float
		];
	}
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Structure\Traits\StringPropertiesExtractor)
	/** {@inheritdoc} */
	protected static function extractStringProperties(string $string): ?array
	{
		return [
			'value' => $string
		];
	}
}
