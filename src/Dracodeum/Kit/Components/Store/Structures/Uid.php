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
 * @property mixed $id
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
		$this->addProperty('id');
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
}
