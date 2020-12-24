<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Prototypes\{
	Type as Prototype,
	Types as Prototypes
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Primitives\Error;

/**
 * This component represents a type which checks and normalizes a value.
 * 
 * If a prototype is given as a name prefixed with a question mark character (<samp>?</samp>), 
 * then that character is stripped from the given name and the type is set as nullable.
 * 
 * @property-read bool $nullable [default = false]
 * <p>Allow a <code>null</code> value.</p>
 * @see \Dracodeum\Kit\Prototypes\Type
 */
class Type extends Component
{
	//Private properties
	private bool $nullable = false;
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getPrototypeBaseClass(): string
	{
		return Prototype::class;
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
			'nullable' => $this->createProperty()->setMode('r')->setAsBoolean()->bind(self::class),
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
	
	
	
	//Public methods
	/**
	 * Get name.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName(): string
	{
		return $this->getPrototype()->getName();
	}
	
	/**
	 * Check if is scalar.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is scalar.</p>
	 */
	public function isScalar(): bool
	{
		return $this->getPrototype()->isScalar();
	}
	
	
	
	//Final public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param coercible:enum(Dracodeum\Kit\Components\Type\Enumerations\Context) $context
	 * <p>The context to process with.</p>
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * <p>An error instance if the given value failed to be processed or <code>null</code> if otherwise.</p>
	 */
	final public function processValue(mixed &$value, $context): ?Error
	{
		//initialize
		$context = EContext::coerceValue($context);
		
		
		//TODO
		
	}
}
