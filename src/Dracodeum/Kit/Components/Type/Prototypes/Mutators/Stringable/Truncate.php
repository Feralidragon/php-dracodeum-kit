<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This prototype truncates a given stringable value to an exact length.
 * 
 * @property-write int $length [writeonce] [transient]  
 * The length to truncate to.
 * 
 * @property-write bool $unicode [writeonce] [transient] [default = false]  
 * Truncate as Unicode.
 * 
 * @property-write bool $ellipsis [writeonce] [transient] [default = false]  
 * Add an ellipsis at the end.
 * 
 * @property-write string|null $ellipsis_string [writeonce] [transient] [default = null]  
 * The ellipsis string to use.  
 * If not set, then the internal default ellipsis string is used.
 * 
 * @property-write bool $keep_words [writeonce] [transient] [default = false]  
 * Try to keep words preserved.
 * 
 * @property-write bool $keep_sentences [writeonce] [transient] [default = false]  
 * Try to keep sentences preserved.
 */
class Truncate extends Prototype implements IExplanationProducer
{
	//Protected properties
	protected int $length;
	
	protected bool $unicode = false;
	
	protected bool $ellipsis = false;
	
	protected ?string $ellipsis_string = null;
	
	protected bool $keep_words = false;
	
	protected bool $keep_sentences = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		$value = UText::truncate($value, $this->length, [
			'unicode' => $this->unicode,
			'ellipsis' => $this->ellipsis,
			'ellipsis_string' => $this->ellipsis_string,
			'keep_words' => $this->keep_words,
			'keep_sentences' => $this->keep_sentences
		]);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		return Text::build()
			->setString("The value is truncated to {{length}} character.")
			->setPluralString("The value is truncated to {{length}} characters.")
			->setPluralNumberPlaceholder('length')
			->setPluralNumber($this->length)
			->setAsLocalized(self::class)
		;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('length');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'length' => $this->createProperty()->setMode('w--')->setAsInteger(true)->bind(self::class),
			'unicode', 'ellipsis', 'keep_words', 'keep_sentences'
				=> $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			'ellipsis_string' => $this->createProperty()->setMode('w--')->setAsString(false, true)->bind(self::class),
			default => null
		};
	}
}
