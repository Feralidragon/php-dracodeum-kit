<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Types;

use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Call as UCall;
use Dracodeum\Kit\Root\System;
use Closure;

/**
 * This prototype represents a callable.
 * 
 * Only a callable is allowed.
 * 
 * @property-write callable|null $template [writeonce] [transient] [default = null]  
 * The template callable to check the signature compatibility against.
 * 
 * @property-write bool $assertive [writeonce] [transient] [default = false]  
 * Process in an assertive manner: perform the slower validations, such as template signature compatibility check, 
 * only when in a debugging environment.
 * 
 * @property-write bool $closure [writeonce] [transient] [default = false]  
 * Coerce into a `Closure` instance.
 */
class TCallable extends Prototype implements ITextifier
{
	//Protected properties
	/** @var callable|null */
	protected $template = null;
	
	protected bool $assertive = false;
	
	protected bool $closure = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function process(mixed &$value, $context): ?Error
	{
		//check
		if (!is_callable($value)) {
			return Error::build(text: "Only a callable is allowed.");
		}
		
		//template
		if (
			$this->template !== null && (!$this->assertive || System::isDebug()) && 
			!UCall::compatible($value, $this->template)
		) {
			$text = Text::build("Only a callable with a signature compatible with {{signature}} is allowed.")
				->setParameter('signature', UCall::signature($this->template))
				->setPlaceholderAsQuoted('signature')
			;
			return Error::build(text: $text);
		}
		
		//closure
		if ($this->closure && !($value instanceof Closure)) {
			$value = Closure::fromCallable($value);
		}
		
		//return
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier)
	/** {@inheritdoc} */
	public function textify(mixed $value)
	{
		$name = UCall::name($value, true);
		if ($name === null) {
			$reflection = UCall::reflection($value);
			$name = Text::build("anonymous@{{filename}}:{{line}}")->setParameters([
				'filename' => $reflection->getFileName(),
				'line' => $reflection->getStartLine()
			]);
		}
		return Text::build("callable<{{name}}>")->setParameter('name', $name);
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'template' => $this->createProperty()->setMode('w--')->setAsCallable(null, true)->bind(self::class),
			'assertive', 'closure' => $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class),
			default => null
		};
	}
}
