<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs;

use Feralygon\Kit\Core\Prototypes\Input;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Interfaces\{
	Information as IInformation,
	Modifiers as IModifiers
};
use Feralygon\Kit\Core\Components\Input\Components\Modifier;
use Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Root\Locale;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core text input prototype class.
 * 
 * This input prototype represents a text or string, for which only integers, floats and strings may be evaluated as such.
 * 
 * @since 1.0.0
 * @property-read bool $unicode [default = false] <p>Set as Unicode text.</p>
 * @see https://en.wikipedia.org/wiki/Plain_text
 * @see https://en.wikipedia.org/wiki/String_(computer_science)
 * @see \Feralygon\Kit\Core\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints\Values [modifier, name = 'constraints.values' or 'constraints.non_values']
 */
class Text extends Input implements IPrototypeProperties, IInformation, IModifiers
{
	//Private properties
	/** @var bool */
	private $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'text';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value) : bool
	{
		//evaluate
		if (!UType::evaluateString($value)) {
			return false;
		}
		
		//unicode
		if ($this->unicode) {
			$encoding = mb_detect_encoding($value);
			$locale_encoding = Locale::getEncoding();
			if ($encoding !== false) {
				$value = mb_convert_encoding($value, $locale_encoding, $encoding);
			} elseif ($locale_encoding === 'UTF-8') {
				$value = utf8_encode($value);
			}
		}
		
		//return
		return true;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'unicode':
				return $this->createProperty()
					->setMode('r')
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->unicode;
					})
					->setSetter(function (bool $unicode) : void {
						$this->unicode = $unicode;
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return [];
	}
	
	
	
	//Implemented public methods (core input prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @description Core text input prototype label (technical).
			 * @tags core prototype input text label technical
			 */
			return UText::localize("String", 'core.prototypes.inputs.text', $text_options);
		}
		
		//non-technical
		/**
		 * @description Core text input prototype label.
		 * @tags core prototype input text label non-technical
		 */
		return UText::localize("Text", 'core.prototypes.inputs.text', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @description Core text input prototype description (technical).
			 * @tags core prototype input text description technical
			 */
			return UText::localize("A string of characters.", 'core.prototypes.inputs.text', $text_options);
		}
		
		//non-technical
		/**
		 * @description Core text input prototype description.
		 * @tags core prototype input text description non-technical
		 */
		return UText::localize("A text.", 'core.prototypes.inputs.text', $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @description Core text input prototype message (technical).
			 * @tags core prototype input text message technical
			 */
			return UText::localize("The given value must be a string of characters.", 'core.prototypes.inputs.text', $text_options);
		}
		
		//non-technical
		/**
		 * @description Core text input prototype message.
		 * @tags core prototype input text message non-technical
		 */
		return UText::localize("The given value must be a text.", 'core.prototypes.inputs.text', $text_options);
	}
	
	
	
	//Implemented public methods (core input prototype modifiers interface)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $prototype_properties = [], array $properties = []) : ?Modifier
	{
		switch ($name) {
			case 'constraints.values':
				return $this->createConstraint(Constraints\Values::class, $prototype_properties, $properties);
			case 'constraints.non_values':
				return $this->createConstraint(Constraints\Values::class, ['negate' => true] + $prototype_properties, $properties);
		}
		return null;
	}
}
