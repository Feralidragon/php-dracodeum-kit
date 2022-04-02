<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Exception;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Utilities\{
	Data as UData,
	Text as UText
};

/**
 * @property-read array $values
 * The values, as a set of `name => value` pairs.
 * 
 * @property-read \Dracodeum\Kit\Primitives\Error[] $errors
 * The error instances, as a set of `name => error` pairs.
 */
class Invalid extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return count($this->values) === 1
			? "Invalid value given for the following property in {{manager.getOwner()}}: {{values}}, " . 
				"with the following error: {{errors}}"
			: "Invalid values given for the following properties in {{manager.getOwner()}}:\n" . 
				"{{values}}\n" . 
				"with the following errors:\n" . 
				"{{errors}}";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('values')->setAsArray(fn (&$key, &$value): bool => is_string($key), non_empty: true);
		$this->addProperty('errors')->setAsArray(
			function (&$key, &$value): bool {
				return is_string($key) && $value instanceof Error;
			},
			non_empty: true
		);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		//placeholder
		switch ($placeholder) {
			//values
			case 'values':
				//single
				if (count($value) === 1) {
					return UData::kfirst($value) . " = " . 
						parent::getPlaceholderValueString($placeholder, UData::first($value));
				}
				
				//multiple
				$strings = [];
				foreach ($value as $name => $v) {
					$strings[] = "{$name} = " . parent::getPlaceholderValueString($placeholder, $v) . ";";
				}
				return UText::mbulletify($strings, options: ['bullet' => '*', 'merge' => true]);
				
			//errors
			case 'errors':
				//single
				if (count($value) === 1) {
					return UText::formatMessage(
						UData::first($value)->getText()->toString(['info_level' => EInfoLevel::INTERNAL]),
						indentation_expression: "   "
					);
				}
				
				//multiple
				$strings = [];
				foreach ($value as $name => $v) {
					$strings[] = "{$name}: " . UText::formatMessage(
						$v->getText()->toString(['info_level' => EInfoLevel::INTERNAL]),
						indentation_expression: "   "
					);
				}
				return UText::mbulletify($strings, options: ['bullet' => '-', 'merge' => true]);
		}
		
		//parent
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
