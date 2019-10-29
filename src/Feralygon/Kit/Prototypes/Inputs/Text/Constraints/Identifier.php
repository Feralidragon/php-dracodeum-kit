<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\{
	InfoScope as EInfoScope,
	TextCase as ETextCase
};
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a given text input value to an identifier format.
 * 
 * @property-write bool $extended [writeonce] [transient] [coercive] [default = false]
 * <p>Allow an extended format, where dots may be used as delimiters between words to represent pointers.</p>
 * @property-write int|null $case [coercive = enumeration value] [default = null]
 * <p>The case to use, as a value from the <code>Feralygon\Kit\Enumerations\TextCase</code> enumeration.</p>
 */
class Identifier extends Constraint implements ISubtype, IInformation, ISchemaData
{
	//Protected properties
	/** @var bool */
	protected $extended = false;
	
	/** @var int|null */
	protected $case = null;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'identifier';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (UType::evaluateString($value) && UText::isIdentifier($value, $this->extended)) {
			return !isset($this->case) || 
				($this->case === ETextCase::LOWER && strtolower($value) === $value) || 
				($this->case === ETextCase::UPPER && strtoupper($value) === $value);
		}
		return false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'text';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		//case
		if ($this->case === ETextCase::LOWER) {
			return $this->extended
				? UText::localize("Extended lowercase identifier format", self::class, $text_options)
				: UText::localize("Lowercase identifier format", self::class, $text_options);
		} elseif ($this->case === ETextCase::UPPER) {
			return $this->extended
				? UText::localize("Extended uppercase identifier format", self::class, $text_options)
				: UText::localize("Uppercase identifier format", self::class, $text_options);
		}
		
		//default
		return $this->extended
			? UText::localize("Extended identifier format", self::class, $text_options)
			: UText::localize("Identifier format", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//entries
		$entries = [];
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			if ($this->case === ETextCase::LOWER) {
				//first
				/**
				 * @description Format description first entry.
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example must start with a lowercase letter (a-z) or underscore (_)
				 */
				$entries[] = UText::localize(
					"must start with a lowercase letter ({{letters.a}}-{{letters.z}}) or underscore ({{underscore}})", 
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z'],
							'underscore' => '_'
						]
					]
				);
				
				//second
				/**
				 * @description Format description second entry.
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @placeholder digits.num0 The numeric "0" digit character.
				 * @placeholder digits.num9 The numeric "9" digit character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example must be only composed by lowercase letters (a-z), digits (0-9) and underscores (_)
				 */
				$entries[] = UText::localize(
					"must be only composed by lowercase letters ({{letters.a}}-{{letters.z}}), " . 
						"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z'],
							'digits' => ['num0' => '0', 'num9' => '9'],
							'underscore' => '_'
						]
					]
				);
				
			} elseif ($this->case === ETextCase::UPPER) {
				//first
				/**
				 * @description Format description first entry.
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example must start with an uppercase letter (A-Z) or underscore (_)
				 */
				$entries[] = UText::localize(
					"must start with an uppercase letter ({{letters.A}}-{{letters.Z}}) or underscore ({{underscore}})", 
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['A' => 'A', 'Z' => 'Z'],
							'underscore' => '_'
						]
					]
				);
				
				//second
				/**
				 * @description Format description second entry.
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @placeholder digits.num0 The numeric "0" digit character.
				 * @placeholder digits.num9 The numeric "9" digit character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example must be only composed by uppercase letters (A-Z), digits (0-9) and underscores (_)
				 */
				$entries[] = UText::localize(
					"must be only composed by uppercase letters ({{letters.A}}-{{letters.Z}}), " . 
						"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['A' => 'A', 'Z' => 'Z'],
							'digits' => ['num0' => '0', 'num9' => '9'],
							'underscore' => '_'
						]
					]
				);
				
			} else {
				//first
				/**
				 * @description Format description first entry.
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example must start with a letter (a-z or A-Z) or underscore (_)
				 */
				$entries[] = UText::localize(
					"must start with a letter ({{letters.a}}-{{letters.z}} or {{letters.A}}-{{letters.Z}}) " . 
						"or underscore ({{underscore}})", 
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
							'underscore' => '_'
						]
					]
				);
				
				//second
				/**
				 * @description Format description second entry.
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @placeholder digits.num0 The numeric "0" digit character.
				 * @placeholder digits.num9 The numeric "9" digit character.
				 * @placeholder underscore The underscore "_" character.
				 * @tags end-user
				 * @example must be only composed by letters (a-z or A-Z), digits (0-9) and underscores (_)
				 */
				$entries[] = UText::localize(
					"must be only composed by letters ({{letters.a}}-{{letters.z}} " . 
						"or {{letters.A}}-{{letters.Z}}), digits ({{digits.num0}}-{{digits.num9}}) " . 
						"and underscores ({{underscore}})",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
							'digits' => ['num0' => '0', 'num9' => '9'],
							'underscore' => '_'
						]
					]
				);
			}
			
		} elseif ($this->case === ETextCase::LOWER) {
			//first
			/**
			 * @description Format description first entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must start with an ASCII lowercase letter (a-z) or underscore (_)
			 */
			$entries[] = UText::localize(
				"must start with an ASCII lowercase letter ({{letters.a}}-{{letters.z}}) " . 
					"or underscore ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z'],
						'underscore' => '_'
					]
				]
			);
			
			//second
			/**
			 * @description Format description second entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must be exclusively composed by ASCII lowercase letters (a-z), digits (0-9) and underscores (_)
			 */
			$entries[] = UText::localize(
				"must be exclusively composed by ASCII lowercase letters ({{letters.a}}-{{letters.z}}), " . 
					"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'underscore' => '_'
					]
				]
			);
			
		} elseif ($this->case === ETextCase::UPPER) {
			//first
			/**
			 * @description Format description first entry.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must start with an ASCII uppercase letter (A-Z) or underscore (_)
			 */
			$entries[] = UText::localize(
				"must start with an ASCII uppercase letter ({{letters.A}}-{{letters.Z}}) " . 
					"or underscore ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['A' => 'A', 'Z' => 'Z'],
						'underscore' => '_'
					]
				]
			);
			
			//second
			/**
			 * @description Format description second entry.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must be exclusively composed by ASCII uppercase letters (A-Z), digits (0-9) and underscores (_)
			 */
			$entries[] = UText::localize(
				"must be exclusively composed by ASCII uppercase letters ({{letters.A}}-{{letters.Z}}), " . 
					"digits ({{digits.num0}}-{{digits.num9}}) and underscores ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'underscore' => '_'
					]
				]
			);
			
		} else {
			//first
			/**
			 * @description Format description first entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must start with an ASCII letter (a-z or A-Z) or underscore (_)
			 */
			$entries[] = UText::localize(
				"must start with an ASCII letter ({{letters.a}}-{{letters.z}} or {{letters.A}}-{{letters.Z}}) " . 
					"or underscore ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'underscore' => '_'
					]
				]
			);
			
			//second
			/**
			 * @description Format description second entry.
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @placeholder digits.num0 The numeric "0" digit character.
			 * @placeholder digits.num9 The numeric "9" digit character.
			 * @placeholder underscore The underscore "_" character.
			 * @tags non-end-user
			 * @example must be exclusively composed by ASCII letters (a-z or A-Z), digits (0-9) and underscores (_)
			 */
			$entries[] = UText::localize(
				"must be exclusively composed by ASCII letters ({{letters.a}}-{{letters.z}} " . 
					"or {{letters.A}}-{{letters.Z}}), digits ({{digits.num0}}-{{digits.num9}}) " . 
					"and underscores ({{underscore}})",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z'],
						'digits' => ['num0' => '0', 'num9' => '9'],
						'underscore' => '_'
					]
				]
			);
		}
		
		//extended entry
		if ($this->extended) {
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @description Format description third entry.
				 * @placeholder dot The dot "." character.
				 * @tags end-user
				 * @example dots (.) may also be used as delimiters between words
				 */
				$entries[] = UText::localize(
					"dots ({{dot}}) may also be used as delimiters between words",
					self::class, $text_options, ['parameters' => ['dot' => '.']]
				);
			} else {
				/**
				 * @description Format description third entry.
				 * @placeholder dot The dot "." character.
				 * @tags non-end-user
				 * @example dots (.) may also be used as delimiters between words to represent pointers
				 */
				$entries[] = UText::localize(
					"dots ({{dot}}) may also be used as delimiters between words to represent pointers",
					self::class, $text_options, ['parameters' => ['dot' => '.']]
				);
			}
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/**
			 * @placeholder entries The format description entries.
			 * @tags end-user
			 * @example Only the following format is allowed:
			 *  &#8226; must start with a letter (a-z and A-Z) or underscore (_);
			 *  &#8226; must be only composed by letters (a-z and A-Z), digits (0-9) and underscores (_);
			 *  &#8226; dots (.) may also be used as delimiters between words.
			 */
			return UText::localize(
				"Only the following format is allowed:\n{{entries}}",
				self::class, $text_options, [
					'parameters' => [
						'entries' => UText::mbulletify($entries, $text_options, ['merge' => true, 'punctuate' => true])
					]
				]
			);
		}
		
		//non-end-user
		/**
		 * @placeholder entries The format description entries.
		 * @tags non-end-user
		 * @example Only an identifier with the following format is allowed:
		 *  &#8226; must start with an ASCII letter (a-z and A-Z) or underscore (_);
		 *  &#8226; must be exclusively composed by ASCII letters (a-z and A-Z), digits (0-9) and underscores (_);
		 *  &#8226; dots (.) may also be used as delimiters between words to represent pointers.
		 */
		return UText::localize(
			"Only an identifier with the following format is allowed:\n{{entries}}",
			self::class, $text_options, [
				'parameters' => [
					'entries' => UText::mbulletify($entries, $text_options, ['merge' => true, 'punctuate' => true])
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'extended' => $this->extended,
			'case' => isset($this->case) ? ETextCase::getName($this->case) : null
		];
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'extended':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
			case 'case':
				return $this->createProperty()
					->setMode('w--')
					->setAsEnumerationValue(ETextCase::class, true)
					->bind(self::class)
				;
		}
		return null;
	}
}
