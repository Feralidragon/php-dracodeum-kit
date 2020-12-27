<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Prototypes\Type\Contract as IPrototypeContract;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Prototypes\{
	Type as Prototype,
	Types as Prototypes
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Primitives\{
	Text,
	Error
};

/**
 * This component represents a type which validates and normalizes values.
 * 
 * If a prototype is given as a name prefixed with a question mark character (<samp>?</samp>), 
 * then that character is stripped from the given name and the type is set as nullable.
 * 
 * @property-read bool $nullable [default = false]
 * <p>Allow a <code>null</code> value.</p>
 * @property enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context [default = INTERNAL]
 * <p>The context to use.</p>
 * @see \Dracodeum\Kit\Prototypes\Type
 * @see \Dracodeum\Kit\Prototypes\Types\Boolean
 * [prototype, name = 'boolean' or 'bool']
 */
class Type extends Component implements IPrototypeContract
{
	//Private properties
	private bool $nullable = false;
	
	/** @var enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) */
	private $context = EContext::INTERNAL;
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Prototypes\Type\Contract)
	/** {@inheritdoc} */
	final public function getContext()
	{
		return $this->context;
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PreInitializer)
	/** {@inheritdoc} */
	protected function preInitialize(&$prototype, array &$properties): void
	{
		if (is_string($prototype) && isset($prototype[0]) && $prototype[0] === '?') {
			$prototype = substr($prototype, 1);
			$properties['nullable'] = true;
		}
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'nullable' => $this->createProperty()->setMode('r+')->setAsBoolean()->bind(self::class),
			'context' => $this->createProperty()->setAsEnumerationValue(EContext::class)->bind(self::class),
			default => null
		};
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		return match ($name) {
			'boolean', 'bool' => Prototypes\Boolean::class,
			default => null
		};
	}
	
	
	
	//Final public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process.</p>
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * <p>An error instance if the given value failed to be processed or <code>null</code> if otherwise.</p>
	 */
	final public function process(mixed &$value): ?Error
	{
		//initialize
		$v = $value;
		$prototype = $this->getPrototype();
		
		//nullable
		if ($v === null && $this->nullable) {
			return null;
		}
		
		//process
		$error = $prototype->process($v);
		if ($error !== null) {
			if (!$error->hasText()) {
				$error->setText(Text::build("The given value is invalid.")->setAsLocalized(self::class));
			}
			return $error;
		}
		
		//finalize
		$value = $v;
		
		//return
		return null;
	}
}
