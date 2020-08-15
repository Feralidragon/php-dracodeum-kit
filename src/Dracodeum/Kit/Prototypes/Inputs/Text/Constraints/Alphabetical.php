<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Text\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\{
	InfoScope as EInfoScope,
	TextCase as ETextCase
};
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype restricts a given text input value to alphabetical characters.
 * 
 * @property-write int|null $case [coercive = enumeration value] [default = null]
 * <p>The case to use, as a value from the <code>Dracodeum\Kit\Enumerations\TextCase</code> enumeration.</p>
 * @property-write bool $unicode [writeonce] [transient] [coercive] [default = false]
 * <p>Check a given text input value as Unicode.</p>
 * @see \Dracodeum\Kit\Enumerations\TextCase
 */
class Alphabetical extends Constraint implements ISubtype, IInformation, ISchemaData
{
	//Protected properties
	/** @var int|null */
	protected $case = null;
	
	/** @var bool */
	protected $unicode = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'alphabetical';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		if (UType::evaluateString($value) && preg_match($this->unicode ? '/^\pL*$/u' : '/^[a-z]*$/i', $value)) {
			return !isset($this->case) || 
				($this->case === ETextCase::LOWER && UText::lower($value, $this->unicode) === $value) || 
				($this->case === ETextCase::UPPER && UText::upper($value, $this->unicode) === $value);
		}
		return false;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'text';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		if ($this->case === ETextCase::LOWER) {
			return UText::localize("Lowercase alphabetic characters only", self::class, $text_options);
		} elseif ($this->case === ETextCase::UPPER) {
			return UText::localize("Uppercase alphabetic characters only", self::class, $text_options);
		}
		return UText::localize("Alphabetic characters only", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//unicode
		if ($this->unicode) {
			if ($this->case === ETextCase::LOWER) {
				return UText::localize("Only lowercase alphabetic characters are allowed.", self::class, $text_options);
			} elseif ($this->case === ETextCase::UPPER) {
				return UText::localize("Only uppercase alphabetic characters are allowed.", self::class, $text_options);
			}
			return UText::localize("Only alphabetic characters are allowed.", self::class, $text_options);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			//case
			if ($this->case === ETextCase::LOWER) {
				/**
				 * @placeholder letters.a The lowercase "a" letter character.
				 * @placeholder letters.z The lowercase "z" letter character.
				 * @tags end-user
				 * @example Only lowercase alphabetic characters (a-z) are allowed.
				 */
				return UText::localize(
					"Only lowercase alphabetic characters ({{letters.a}}-{{letters.z}}) are allowed.",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['a' => 'a', 'z' => 'z']
						]
					]
				);
			} elseif ($this->case === ETextCase::UPPER) {
				/**
				 * @placeholder letters.A The uppercase "A" letter character.
				 * @placeholder letters.Z The uppercase "Z" letter character.
				 * @tags end-user
				 * @example Only uppercase alphabetic characters (A-Z) are allowed.
				 */
				return UText::localize(
					"Only uppercase alphabetic characters ({{letters.A}}-{{letters.Z}}) are allowed.",
					self::class, $text_options, [
						'parameters' => [
							'letters' => ['A' => 'A', 'Z' => 'Z']
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @tags end-user
			 * @example Only alphabetic characters (a-z and A-Z) are allowed.
			 */
			return UText::localize(
				"Only alphabetic characters ({{letters.a}}-{{letters.z}} and {{letters.A}}-{{letters.Z}}) are allowed.",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z']
					]
				]
			);
		}
		
		//case
		if ($this->case === ETextCase::LOWER) {
			/**
			 * @placeholder letters.a The lowercase "a" letter character.
			 * @placeholder letters.z The lowercase "z" letter character.
			 * @tags non-end-user
			 * @example Only ASCII lowercase alphabetic characters (a-z) are allowed.
			 */
			return UText::localize(
				"Only ASCII lowercase alphabetic characters ({{letters.a}}-{{letters.z}}) are allowed.",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['a' => 'a', 'z' => 'z']
					]
				]
			);
		} elseif ($this->case === ETextCase::UPPER) {
			/**
			 * @placeholder letters.A The uppercase "A" letter character.
			 * @placeholder letters.Z The uppercase "Z" letter character.
			 * @tags non-end-user
			 * @example Only ASCII uppercase alphabetic characters (A-Z) are allowed.
			 */
			return UText::localize(
				"Only ASCII uppercase alphabetic characters ({{letters.A}}-{{letters.Z}}) are allowed.",
				self::class, $text_options, [
					'parameters' => [
						'letters' => ['A' => 'A', 'Z' => 'Z']
					]
				]
			);
		}
		
		//default
		/**
		 * @placeholder letters.a The lowercase "a" letter character.
		 * @placeholder letters.z The lowercase "z" letter character.
		 * @placeholder letters.A The uppercase "A" letter character.
		 * @placeholder letters.Z The uppercase "Z" letter character.
		 * @tags non-end-user
		 * @example Only ASCII alphabetic characters (a-z and A-Z) are allowed.
		 */
		return UText::localize(
			"Only ASCII alphabetic characters ({{letters.a}}-{{letters.z}} and " . 
				"{{letters.A}}-{{letters.Z}}) are allowed.",
			self::class, $text_options, [
				'parameters' => [
					'letters' => ['a' => 'a', 'z' => 'z', 'A' => 'A', 'Z' => 'Z']
				]
			]
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'case' => isset($this->case) ? ETextCase::getName($this->case) : null,
			'unicode' => $this->unicode
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'case':
				return $this->createProperty()
					->setMode('w--')
					->setAsEnumerationValue(ETextCase::class, true)
					->bind(self::class)
				;
			case 'unicode':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
