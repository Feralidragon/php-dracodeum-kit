<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype as ISubtype;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * @property-write bool $unicode [writeonce] [transient] [coercive] [default = false]
 * <p>Check a given text input value as Unicode.</p>
 */
class Values extends Constraints\Values implements ISubtype
{
	//Protected properties
	/** @var bool */
	protected $unicode = false;
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'text';
	}
	
	
	
	//Final protected methods
	/**
	 * Check if is words only.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is words only.</p>
	 */
	final protected function isWordsOnly(): bool
	{
		foreach ($this->values as $value) {
			if (!UText::isWord($value, $this->unicode)) {
				return false;
			}
		}
		return true;
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/** @tags technical */
				return UText::plocalize(
					"Disallowed string", "Disallowed strings",
					count($this->values), null, self::class, $text_options
				);
			}
			
			//words-only
			if ($this->isWordsOnly()) {
				/** @tags non-technical */
				return UText::plocalize(
					"Disallowed word", "Disallowed words",
					count($this->values), null, self::class, $text_options
				);
			}
			
			//default
			/** @tags non-technical */
			return UText::plocalize(
				"Disallowed text", "Disallowed texts",
				count($this->values), null, self::class, $text_options
			);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::plocalize(
				"Allowed string", "Allowed strings",
				count($this->values), null, self::class, $text_options
			);
		}
		
		//words-only
		if ($this->isWordsOnly()) {
			/** @tags non-technical */
			return UText::plocalize(
				"Allowed word", "Allowed words",
				count($this->values), null, self::class, $text_options
			);
		}
		
		//default
		/** @tags non-technical */
		return UText::plocalize(
			"Allowed text", "Allowed texts",
			count($this->values), null, self::class, $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//negate
		if ($this->negate) {
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder values The list of disallowed text values.
				 * @tags technical
				 * @example The following strings are not allowed: "foo", "bar" and "abc".
				 */
				return UText::plocalize(
					"The following string is not allowed: {{values}}.",
					"The following strings are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			}
			
			//words-only
			if ($this->isWordsOnly()) {
				/**
				 * @placeholder values The list of disallowed text values.
				 * @tags non-technical
				 * @example The following words are not allowed: "foo", "bar" and "abc".
				 */
				return UText::plocalize(
					"The following word is not allowed: {{values}}.",
					"The following words are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			}
			
			//default
			/**
			 * @placeholder values The list of disallowed text values.
			 * @tags non-technical
			 * @example The following texts are not allowed: "foo", "bar" and "abc".
			 */
			return UText::plocalize(
				"The following text is not allowed: {{values}}.",
				"The following texts are not allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @placeholder values The list of allowed text values.
			 * @tags technical
			 * @example Only the following strings are allowed: "foo", "bar" or "abc".
			 */
			return UText::plocalize(
				"Only the following string is allowed: {{values}}.",
				"Only the following strings are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//words-only
		if ($this->isWordsOnly()) {
			/**
			 * @placeholder values The list of allowed text values.
			 * @tags non-technical
			 * @example Only the following words are allowed: "foo", "bar" or "abc".
			 */
			return UText::plocalize(
				"Only the following word is allowed: {{values}}.",
				"Only the following words are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//default
		/**
		 * @placeholder values The list of allowed text values.
		 * @tags non-technical
		 * @example Only the following texts are allowed: "foo", "bar" or "abc".
		 */
		return UText::plocalize(
			"Only the following text is allowed: {{values}}.",
			"Only the following texts are allowed: {{values}}.",
			count($this->values), null, self::class, $text_options, [
				'parameters' => ['values' => $this->getString($text_options)]
			]
		);
	}
	
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return parent::getSchemaData() + [
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UType::evaluateString($value);
	}
	
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'unicode':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return parent::buildProperty($name);
	}
}
