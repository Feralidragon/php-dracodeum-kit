<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Core\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core input truncate filter modifier prototype class.
 * 
 * This filter prototype truncates a value to a specific length.
 * 
 * @since 1.0.0
 * @property int $length <p>The length to truncate to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property bool $unicode [default = false] <p>Handle as an Unicode value.</p>
 * @property bool $ellipsis [default = false] <p>Add an ellipsis at the end of the truncated value.</p>
 * @property string|null $ellipsis_string [default = null] <p>The ellipsis string to use.<br>
 * If not set, the internal default ellipsis string is used.</p>
 * @property bool $keep_words [default = false] <p>Try to keep words preserved in the truncated value.</p>
 * @property bool $keep_sentences [default = false] <p>Try to keep sentences preserved in the truncated value.</p>
 */
class Truncate extends Filter implements IPrototypeProperties, IName, IInformation, IStringification, ISchemaData
{
	//Private properties
	/** @var int */
	private $length;
	
	/** @var bool */
	private $unicode = false;
	
	/** @var bool */
	private $ellipsis = false;
	
	/** @var string|null */
	private $ellipsis_string = null;
	
	/** @var bool */
	private $keep_words = false;
	
	/** @var bool */
	private $keep_sentences = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value) : bool
	{
		if (is_string($value)) {
			$value = UText::truncate($value, $this->length, [
				'unicode' => $this->unicode,
				'ellipsis' => $this->ellipsis,
				'ellipsis_string' => $this->ellipsis_string,
				'keep_words' => $this->keep_words,
				'keep_sentences' => $this->keep_sentences
			]);
			return true;
		}
		return false;
	}
	
	
	
	//Implemented public methods (core prototype properties interface)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'length':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateInteger($value) && $value >= 0;
					})
					->setGetter(function () : int {
						return $this->length;
					})
					->setSetter(function (int $length) : void {
						$this->length = $length;
					})
				;
			case 'unicode':
				return $this->createProperty()
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
			case 'ellipsis':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->ellipsis;
					})
					->setSetter(function (bool $ellipsis) : void {
						$this->ellipsis = $ellipsis;
					})
				;
			case 'ellipsis_string':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateString($value, true);
					})
					->setGetter(function () : ?string {
						return $this->ellipsis_string;
					})
					->setSetter(function (?string $ellipsis_string) : void {
						$this->ellipsis_string = $ellipsis_string;
					})
				;
			case 'keep_words':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->keep_words;
					})
					->setSetter(function (bool $keep_words) : void {
						$this->keep_words = $keep_words;
					})
					;
			case 'keep_sentences':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateBoolean($value);
					})
					->setGetter(function () : bool {
						return $this->keep_sentences;
					})
					->setSetter(function (bool $keep_sentences) : void {
						$this->keep_sentences = $keep_sentences;
					})
				;
		}
		return null;
	}
	
	
	
	//Implemented public static methods (core prototype properties interface)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['length'];
	}
	
	
	
	//Implemented public methods (core input modifier prototype name interface)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'filters.truncate';
	}
	
	
	
	//Implemented public methods (core input modifier prototype information interface)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Truncated length", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		/**
		 * @placeholder length The truncated length.
		 * @example The value is truncated to 100 characters.
		 */
		return UText::plocalize(
			"The value is truncated to {{length}} character.",
			"The value is truncated to {{length}} characters.",
			$this->length, 'length', self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (core input modifier prototype stringification interface)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->length, $text_options);
	}
	
	
	
	//Implemented public methods (core input modifier prototype schema data interface)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'length' => $this->length,
			'unicode' => $this->unicode,
			'ellipsis' => [
				'enable' => $this->ellipsis,
				'string' => $this->ellipsis_string
			],
			'keep' => [
				'words' => $this->keep_words,
				'sentences' => $this->keep_sentences
			]
		];
	}
}
