<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable;

use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This prototype restricts a given stringable value to a set of wildcard matches.
 * 
 * @property-write string[] $wildcards [writeonce] [transient]  
 * The wildcard matches to restrict the given value to.
 * 
 * @property-write bool $insensitive [writeonce] [transient] [default = false]  
 * Match the given wildcards in a case-insensitive manner.
 * 
 * @property-write bool $negate [writeonce] [transient] [default = false]  
 * Negate the restriction condition, so the given wildcards behave as disallowed wildcards instead.
 */
class Wildcards extends Prototype implements IExplanationProducer
{
	//Protected properties
	/** @var string[] */
	protected array $wildcards;
	
	protected bool $insensitive = false;
	
	protected bool $negate = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value)
	{
		return UText::anyWildcardsMatch($value, $this->wildcards, $this->insensitive) !== $this->negate;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer)
	/** {@inheritdoc} */
	public function produceExplanation()
	{
		//text (initialize)
		$text = Text::build()
			->setPluralNumber(count($this->wildcards))
			->setParameter('wildcards', $this->wildcards)
			->setPlaceholderStringifier('wildcards', function (mixed $value, TextOptions $text_options): string {
				return UText::commify($value, $text_options, $this->negate ? 'and' : 'or', true);
			})
			->setAsLocalized(self::class)
		;
		
		//text (finalize)
		if ($this->negate) {
			$text
				->setString("The following match is not allowed: {{wildcards}}.")
				->setPluralString("The following matches are not allowed: {{wildcards}}.")
				->setString("The following wildcard match is not allowed: {{wildcards}}.", EInfoLevel::TECHNICAL)
				->setPluralString(
					"The following wildcard matches are not allowed: {{wildcards}}.", EInfoLevel::TECHNICAL
				)
			;
		} else {
			$text
				->setString("Only the following match is allowed: {{wildcards}}.")
				->setPluralString("Only the following matches are allowed: {{wildcards}}.")
				->setString("Only the following wildcard match is allowed: {{wildcards}}.", EInfoLevel::TECHNICAL)
				->setPluralString(
					"Only the following wildcard matches are allowed: {{wildcards}}.", EInfoLevel::TECHNICAL
				)
			;
		}
		
		//character
		$text->appendText(
			Text::build()
				->setString("The character {{character}} matches any characters.")
				->setString(
					"The wildcard character {{character}} matches any number and type of characters.",
					EInfoLevel::TECHNICAL
				)
				->setParameter('character', '*')
				->setPlaceholderStringifier('character', function (mixed $value, TextOptions $text_options): string {
					return UText::stringify($value, $text_options, ['quote_strings' => true]);
				})
				->setAsLocalized(self::class)
		);
		
		//insensitive
		if ($this->insensitive) {
			$text->appendText(
				Text::build()
					->setString("All matches are case-insensitive.")
					->setString(
						"All wildcard matches are performed in a case-insensitive manner.", EInfoLevel::TECHNICAL
					)
					->setAsLocalized(self::class)
			);
		}
		
		//return
		return $text;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertiesInitializer)
	/** {@inheritdoc} */
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('wildcards');
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'wildcards'
				=> $this->createProperty()
					->setMode('w--')
					->setAsArray(fn (&$key, &$value): bool => UType::evaluateString($value), true, true)
					->bind(self::class)
				,
			'insensitive', 'negate' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
