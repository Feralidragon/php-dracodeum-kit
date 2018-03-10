<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Prototype\Interfaces\Properties as IPrototypeProperties;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototype\Interfaces\Properties)
	/** {@inheritdoc} */
	public function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'length':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateInteger($value) && $value >= 0;
					})
					->bind(self::class)
				;
			case 'unicode':
				//no break
			case 'ellipsis':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
			case 'ellipsis_string':
				return $this->createProperty()->setAsString(false, true)->bind(self::class);
			case 'keep_words':
				//no break
			case 'keep_sentences':
				return $this->createProperty()->setAsBoolean()->bind(self::class);
		}
		return null;
	}
	
	
	
	//Implemented public static methods (Feralygon\Kit\Prototype\Interfaces\Properties)
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['length'];
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'filters.truncate';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Information)
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options) : string
	{
		return UText::stringify($this->length, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\SchemaData)
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
