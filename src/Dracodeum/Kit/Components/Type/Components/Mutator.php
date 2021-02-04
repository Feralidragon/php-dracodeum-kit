<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces as PrototypeInterfaces;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This component represents a mutator which processes and modifies values from a type component.
 * 
 * @method \Dracodeum\Kit\Components\Type\Prototypes\Mutator getPrototype() [protected]
 * 
 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator
 */
class Mutator extends Component
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Final public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * An error instance if the given value failed to be processed, or `null` if otherwise.
	 */
	final public function process(mixed &$value): ?Error
	{
		//initialize
		$v = $value;
		$prototype = $this->getPrototype();
		
		//process
		$error = $prototype->process($v);
		if (is_bool($error)) {
			$error = $error ? null : Error::build();
		} elseif ($error !== null && !($error instanceof Error)) {
			UCall::haltInternal([
				'error_message' => "Invalid return value {{value}} from prototype {{prototype}}.",
				'parameters' => ['value' => $error, 'prototype' => $prototype]
			]);
		}
		
		//error
		if ($error !== null) {
			$error_text = $error->getText() ?? $this->getExplanation() ?? Text::build();
			if (!$error_text->hasString()) {
				$error_text->setString("The given value failed to be processed.")->setAsLocalized(self::class);
			}
			$error->setText($error_text);
			return $error;
		}
		
		//finalize
		$value = $v;
		
		//return
		return null;
	}
	
	/**
	 * Get explanation.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * The explanation, as a text instance, or `null` if none is set.
	 */
	final public function getExplanation(): ?Text
	{
		//initialize
		$prototype = $this->getPrototype();
		
		//return
		return $prototype instanceof PrototypeInterfaces\ExplanationProducer
			? UCall::guardExecution([$prototype, 'produceExplanation'], [], [Text::class, 'coerce'])
			: null;
	}
}
