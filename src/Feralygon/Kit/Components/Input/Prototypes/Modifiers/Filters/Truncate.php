<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation,
	Stringification as IStringification,
	SchemaData as ISchemaData
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This filter prototype truncates a value to a specific length.
 * 
 * @since 1.0.0
 * @property-write int $length [writeonce] [coercive]
 * <p>The length to truncate a given value to.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property-write bool $unicode [writeonce] [coercive] [default = false]
 * <p>Handle a given value as Unicode.</p>
 * @property-write bool $ellipsis [writeonce] [coercive] [default = false]
 * <p>Add an ellipsis at the end of the truncated value.</p>
 * @property-write string|null $ellipsis_string [writeonce] [coercive] [default = null]
 * <p>The ellipsis string to use.<br>
 * If not set, then the internal default ellipsis string is used.</p>
 * @property-write bool $keep_words [writeonce] [coercive] [default = false]
 * <p>Try to keep words preserved in the truncated value.</p>
 * @property-write bool $keep_sentences [writeonce] [coercive] [default = false]
 * <p>Try to keep sentences preserved in the truncated value.</p>
 */
class Truncate extends Filter implements IName, IInformation, IStringification, ISchemaData
{
	//Protected properties
	/** @var int */
	protected $length;
	
	/** @var bool */
	protected $unicode = false;
	
	/** @var bool */
	protected $ellipsis = false;
	
	/** @var string|null */
	protected $ellipsis_string = null;
	
	/** @var bool */
	protected $keep_words = false;
	
	/** @var bool */
	protected $keep_sentences = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (UType::evaluateString($value)) {
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'filters.truncate';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Truncated length", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Stringification)
	/** {@inheritdoc} */
	public function getString(TextOptions $text_options): string
	{
		return UText::stringify($this->length, $text_options);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\RequiredPropertyNamesLoader)
	/** {@inheritdoc} */
	protected function loadRequiredPropertyNames(): void
	{
		$this->addRequiredPropertyName('length');
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'length':
				return $this->createProperty()->setMode('w-')->setAsInteger(true)->bind(self::class);
			case 'unicode':
				//no break
			case 'ellipsis':
				return $this->createProperty()->setMode('w-')->setAsBoolean()->bind(self::class);
			case 'ellipsis_string':
				return $this->createProperty()->setMode('w-')->setAsString(false, true)->bind(self::class);
			case 'keep_words':
				//no break
			case 'keep_sentences':
				return $this->createProperty()->setMode('w-')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
