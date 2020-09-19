<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;
use Dracodeum\Kit\Prototype\Interfaces\Subcontracts as ISubcontracts;
use Dracodeum\Kit\Prototypes\Input\Subcontracts;
use Dracodeum\Kit\Components\Input\Factories\Component as FComponent;
use Dracodeum\Kit\Components\Input\Components\Modifiers\{
	Constraint,
	Filter
};

/**
 * @see \Dracodeum\Kit\Components\Input
 * @see \Dracodeum\Kit\Prototypes\Input\Subcontracts\ConstraintCreator
 * [subcontract, name = 'ConstraintCreator']
 * @see \Dracodeum\Kit\Prototypes\Input\Subcontracts\FilterCreator
 * [subcontract, name = 'FilterCreator']
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\Information
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\ErrorUnset
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\ErrorMessage
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\ValueStringifier
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\SchemaData
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\ConstraintProducer
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\FilterProducer
 * @see \Dracodeum\Kit\Prototypes\Input\Interfaces\ModifierBuilder
 */
abstract class Input extends Prototype implements ISubcontracts
{
	//Abstract public methods
	/**
	 * Get name.
	 * 
	 * The returning name must be a canonical string which identifies this input.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	abstract public function getName(): string;
	
	/**
	 * Check if is scalar.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is scalar.</p>
	 */
	abstract public function isScalar(): bool;
	
	/**
	 * Evaluate a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated.</p>
	 */
	abstract public function evaluateValue(&$value): bool;
	
	
	
	//Implemented public static methods (Dracodeum\Kit\Prototype\Interfaces\Subcontracts)
	/** {@inheritdoc} */
	public static function getSubcontract(string $name): ?string
	{
		switch ($name) {
			case 'ConstraintCreator':
				return Subcontracts\ConstraintCreator::class;
			case 'FilterCreator':
				return Subcontracts\FilterCreator::class;
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Create a constraint instance with a given prototype.
	 * 
	 * @param \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint|string $prototype
	 * <p>The prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to create with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint
	 * <p>The created constraint instance with the given prototype.</p>
	 */
	protected function createConstraint($prototype, array $properties = []): Constraint
	{
		return $this->subcontractCall(
			'ConstraintCreator', 'createConstraint', [FComponent::class, 'constraint'], $prototype, $properties
		);
	}
	
	/**
	 * Create a filter instance with a given prototype.
	 * 
	 * @param \Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter|string $prototype
	 * <p>The prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to create with, as a set of <samp>name => value</samp> pairs, 
	 * if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifiers\Filter
	 * <p>The created filter instance with the given prototype.</p>
	 */
	protected function createFilter($prototype, array $properties = []): Filter
	{
		return $this->subcontractCall(
			'FilterCreator', 'createFilter', [FComponent::class, 'filter'], $prototype, $properties
		);
	}
}
