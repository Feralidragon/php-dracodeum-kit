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
 * @property-write bool $insensitive [writeonce] [transient] [coercive] [default = false]
 * <p>Restrict a given text input value to the given allowed values in a case-insensitive manner.</p>
 * @property-write bool $unicode [writeonce] [transient] [coercive] [default = false]
 * <p>Check a given text input value as Unicode.</p>
 */
class Values extends Constraints\Values implements ISubtype
{
	//Protected properties
	/** @var bool */
	protected $insensitive = false;
	
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
		//label
		$label = '';
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/** @tags technical */
				$label = UText::plocalize(
					"Disallowed string", "Disallowed strings",
					count($this->values), null, self::class, $text_options
				);
			} elseif ($this->isWordsOnly()) {
				/** @tags non-technical */
				$label = UText::plocalize(
					"Disallowed word", "Disallowed words",
					count($this->values), null, self::class, $text_options
				);
			} else {
				/** @tags non-technical */
				$label = UText::plocalize(
					"Disallowed text", "Disallowed texts",
					count($this->values), null, self::class, $text_options
				);
			}
		} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			$label = UText::plocalize(
				"Allowed string", "Allowed strings",
				count($this->values), null, self::class, $text_options
			);
		} elseif ($this->isWordsOnly()) {
			/** @tags non-technical */
			$label = UText::plocalize(
				"Allowed word", "Allowed words",
				count($this->values), null, self::class, $text_options
			);
		} else {
			/** @tags non-technical */
			$label = UText::plocalize(
				"Allowed text", "Allowed texts",
				count($this->values), null, self::class, $text_options
			);
		}
		
		//insensitive
		if ($this->insensitive) {
			/**
			 * @placeholder label The label.
			 * @example Allowed text (case-insensitive)
			 */
			$label = UText::localize(
				"{{label}} (case-insensitive)", self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		}
		
		//return
		return $label;
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//message
		$message = '';
		if ($this->negate) {
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder values The list of disallowed text values.
				 * @tags technical
				 * @example The following strings are not allowed: "foo", "bar" and "abc".
				 */
				$message = UText::plocalize(
					"The following string is not allowed: {{values}}.",
					"The following strings are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			} elseif ($this->isWordsOnly()) {
				/**
				 * @placeholder values The list of disallowed text values.
				 * @tags non-technical
				 * @example The following words are not allowed: "foo", "bar" and "abc".
				 */
				$message = UText::plocalize(
					"The following word is not allowed: {{values}}.",
					"The following words are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			} else {
				/**
				 * @placeholder values The list of disallowed text values.
				 * @tags non-technical
				 * @example The following texts are not allowed: "foo", "bar" and "abc".
				 */
				$message = UText::plocalize(
					"The following text is not allowed: {{values}}.",
					"The following texts are not allowed: {{values}}.",
					count($this->values), null, self::class, $text_options, [
						'parameters' => ['values' => $this->getString($text_options)]
					]
				);
			}
		} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/**
			 * @placeholder values The list of allowed text values.
			 * @tags technical
			 * @example Only the following strings are allowed: "foo", "bar" or "abc".
			 */
			$message = UText::plocalize(
				"Only the following string is allowed: {{values}}.",
				"Only the following strings are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		} elseif ($this->isWordsOnly()) {
			/**
			 * @placeholder values The list of allowed text values.
			 * @tags non-technical
			 * @example Only the following words are allowed: "foo", "bar" or "abc".
			 */
			$message = UText::plocalize(
				"Only the following word is allowed: {{values}}.",
				"Only the following words are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		} else {
			/**
			 * @placeholder values The list of allowed text values.
			 * @tags non-technical
			 * @example Only the following texts are allowed: "foo", "bar" or "abc".
			 */
			$message = UText::plocalize(
				"Only the following text is allowed: {{values}}.",
				"Only the following texts are allowed: {{values}}.",
				count($this->values), null, self::class, $text_options, [
					'parameters' => ['values' => $this->getString($text_options)]
				]
			);
		}
		
		//insensitive
		if ($this->insensitive) {
			$message .= "\n";
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/** @tags technical */
				$message .= UText::localize(
					"The string comparison is performed in a case-insensitive manner.", self::class, $text_options
				);
			} elseif ($this->isWordsOnly()) {
				/** @tags non-technical */
				$message .= UText::localize(
					"The word comparison is performed in a case-insensitive manner.", self::class, $text_options
				);
			} else {
				/** @tags non-technical */
				$message .= UText::localize(
					"The text comparison is performed in a case-insensitive manner.", self::class, $text_options
				);
			}
		}
		
		//return
		return $message;
	}
	
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return parent::getSchemaData() + [
			'insensitive' => $this->insensitive,
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function isValueAllowed($value): bool
	{
		$values = $this->values;
		if ($this->insensitive) {
			$value = UText::lower($value, $this->unicode);
			$values = array_map(function ($value) {
				return UText::lower($value, $this->unicode);
			}, $values);
		}
		return in_array($value, $values, true);
	}
	
	/** {@inheritdoc} */
	protected function evaluateValue(&$value): bool
	{
		return UType::evaluateString($value);
	}
	
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'insensitive':
				//no break
			case 'unicode':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return parent::buildProperty($name);
	}
}
