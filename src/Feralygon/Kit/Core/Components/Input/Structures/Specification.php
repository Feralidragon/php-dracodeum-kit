<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Structures;

use Feralygon\Kit\Core\Structure;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Components\Input\Components\Modifier\Structures\Specification as ModifierSpecification;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core input component specification structure class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read mixed $data [default = null] <p>The data.</p>
 * @property-read \Feralygon\Kit\Core\Components\Input\Components\Modifier\Structures\Specification[] $modifiers [default = []] <p>The modifier specification instances.</p>
 * @see \Feralygon\Kit\Core\Components\Input
 */
class Specification extends Structure
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['name'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'name':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateString($value);
					})
				;
			case 'data':
				return $this->createProperty()
					->setMode('r')
					->setDefaultValue(null)
				;
			case 'modifiers':
				return $this->createProperty()
					->setMode('r')
					->setDefaultValue([])
					->setEvaluator(function (&$value) : bool {
						if (is_array($value)) {
							foreach ($value as $v) {
								if (!is_object($v) || !UType::isA($v, ModifierSpecification::class)) {
									return false;
								}
							}
							return true;
						}
						return false;
					})
				;
		}
		return null;
	}
}
