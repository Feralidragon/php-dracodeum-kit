<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ErrorMessage as IErrorMessage,
	SchemaData as ISchemaData,
	ModifierBuilder as IModifierBuilder,
	ErrorUnset as IErrorUnset
};
use Feralygon\Kit\Primitives\Vector as Primitive;
use Feralygon\Kit\Components\Input as Component;
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\{
	Constraints,
	Filters
};
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This input prototype represents a vector, as an instance of <code>Feralygon\Kit\Primitives\Vector</code>.
 * 
 * Only the following types of values may be evaluated as a vector:<br>
 * &nbsp; &#8226; &nbsp; a <code>Feralygon\Kit\Primitives\Vector</code> instance;<br>
 * &nbsp; &#8226; &nbsp; a non-associative array;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface;<br>
 * &nbsp; &#8226; &nbsp; a string as a comma separated list of values, such as <samp>value1,value2,value3</samp>;<br>
 * &nbsp; &#8226; &nbsp; a JSON array.
 * 
 * @since 1.0.0
 * @property-write \Feralygon\Kit\Components\Input|null $input [writeonce] [default = null]
 * <p>The input instance to evaluate values with.</p>
 * @see https://en.wikipedia.org/wiki/Array_data_structure
 * @see https://en.wikipedia.org/wiki/Sequence_container_(C%2B%2B)#Vector
 * @see \Feralygon\Kit\Primitives\Vector
 * @see \Feralygon\Kit\Interfaces\Arrayable
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints\Length
 * [modifier, name = 'constraints.length' or 'length']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints\MinLength
 * [modifier, name = 'constraints.min_length' or 'min_length']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints\MaxLength
 * [modifier, name = 'constraints.max_length' or 'max_length']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints\LengthRange
 * [modifier, name = 'constraints.length_range' or 'length_range']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints\NonEmpty
 * [modifier, name = 'constraints.non_empty' or 'non_empty']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints\Unique
 * [modifier, name = 'constraints.unique' or 'unique']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Filters\Truncate
 * [modifier, name = 'filters.truncate' or 'truncate']
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Filters\Unique
 * [modifier, name = 'filters.unique']
 */
class Vector extends Input implements IInformation, IErrorMessage, ISchemaData, IModifierBuilder, IErrorUnset
{
	//Protected properties
	/** @var \Feralygon\Kit\Components\Input|null */
	protected $input = null;
	
	/** @var array */
	protected $error_values = [];
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'vector';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return false;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		//string, int or float
		$vector = $value;
		if (is_string($vector) || is_int($vector) || is_float($vector)) {
			$vector = trim($vector);
			if ($vector === '') {
				$vector = [];
			} elseif ($vector[0] === '[') {
				$vector = json_decode($vector, true);
				if (!isset($vector)) {
					return false;
				}
			} else {
				$vector = preg_split('/\s*,\s*/s', $vector);
			}
		}
		
		//evaluate
		if (!Primitive::evaluate($vector)) {
			return false;
		}
		
		//input
		if (isset($this->input)) {
			//evaluate
			foreach ($vector as $i => $v) {
				if ($this->input->setValue($v, true)) {
					$vector->set($i, $this->input->getValue());
					$this->input->unsetValue();
				} else {
					$this->error_values[$i] = $v;
				}
			}
			
			//check
			if (!empty($this->error_values)) {
				return false;
			}
		}
		
		//finish
		$value = $vector;
		return true;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		//input
		if (isset($this->input)) {
			//label
			$input_label = $this->input->getLabel($text_options, $info_options);
			if (UText::multiline($input_label)) {
				$input_label = "\n" . UText::indentate($input_label) . "\n";
			}
			
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder input.label The input label.
				 * @tags end-user
				 * @example List<Text>
				 */
				return UText::localize("List<{{input.label}}>", self::class, $text_options, [
					'parameters' => [
						'input' => ['label' => $input_label]
					]
				]);
			}
			
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder input.label The input label.
				 * @tags technical
				 * @example Array<String>
				 */
				return UText::localize("Array<{{input.label}}>", self::class, $text_options, [
					'parameters' => [
						'input' => ['label' => $input_label]
					]
				]);
			}
			
			//non-end-user and non-technical
			/**
			 * @placeholder input.label The input label.
			 * @tags non-end-user non-technical
			 * @example Vector<Text>
			 */
			return UText::localize("Vector<{{input.label}}>", self::class, $text_options, [
				'parameters' => [
					'input' => ['label' => $input_label]
				]
			]);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("List", self::class, $text_options);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("Array", self::class, $text_options);
		}
		
		//non-end-user and non-technical
		/** @tags non-end-user non-technical */
		return UText::localize("Vector", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//input
		$input_description = isset($this->input) ? $this->input->getDescription($text_options, $info_options) : null;
		if (isset($input_description)) {
			//description
			$input_description = UText::formatMessage($input_description, true);
			
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				//scalar
				if ($this->input->isScalar()) {
					/**
					 * @placeholder input.description The input description.
					 * @tags end-user
					 * @example A list, which may be given as a comma separated list of items, with each one as: a text.
					 */
					return UText::localize(
						"A list, which may be given as a comma separated list of items, " . 
							"with each one as: {{input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'input' => ['description' => $input_description]
							]
						]
					);
				}
				
				//default
				/**
				 * @placeholder input.description The input description.
				 * @tags end-user
				 * @example A list, with each item as: a text.
				 */
				return UText::localize(
					"A list, with each item as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				//scalar
				if ($this->input->isScalar()) {
					/**
					 * @placeholder input.description The input description.
					 * @tags technical
					 * @example An array, which may be given as a comma separated list of values or a JSON array, \
					 * with each value as: a string of characters.
					 */
					return UText::localize(
						"An array, which may be given as a comma separated list of values or a JSON array, " . 
							"with each value as: {{input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'input' => ['description' => $input_description]
							]
						]
					);
				}
				
				//default
				/**
				 * @placeholder input.description The input description.
				 * @tags technical
				 * @example An array, with each value as: a string of characters.
				 */
				return UText::localize(
					"An array, with each value as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//scalar
			if ($this->input->isScalar()) {
				/**
				 * @placeholder input.description The input description.
				 * @tags non-technical non-end-user
				 * @example A vector, which may be given as a comma separated list of values or a JSON array, \
				 * with each value as: a text.
				 */
				return UText::localize(
					"A vector, which may be given as a comma separated list of values or a JSON array, " . 
						"with each value as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder input.description The input description.
			 * @tags non-technical non-end-user
			 * @example A vector, with each value as: a text.
			 */
			return UText::localize(
				"A vector, with each value as: {{input.description}}",
				self::class, $text_options, [
					'parameters' => [
						'input' => ['description' => $input_description]
					]
				]
			);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize(
				"A list, which may be given as a comma separated list of items.",
				self::class, $text_options
			);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize(
				"An array, which may be given as a comma separated list of values or a JSON array.",
				self::class, $text_options
			);
		}
		
		//non-end-user and non-technical
		/** @tags non-end-user non-technical */
		return UText::localize(
			"A vector, which may be given as a comma separated list of values or a JSON array.",
			self::class, $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		//input
		$input_description = isset($this->input) ? $this->input->getDescription($text_options, $info_options) : null;
		if (isset($input_description)) {
			//description
			$input_description = UText::formatMessage($input_description, true);
			
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				//scalar
				if ($this->input->isScalar()) {
					/**
					 * @placeholder input.description The input description.
					 * @tags end-user
					 * @example Only a list is allowed, which may be given as a comma separated list of items, \
					 * with each one as: a text.
					 */
					return UText::localize(
						"Only a list is allowed, which may be given as a comma separated list of items, " . 
							"with each one as: {{input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'input' => ['description' => $input_description]
							]
						]
					);
				}
				
				//default
				/**
				 * @placeholder input.description The input description.
				 * @tags end-user
				 * @example Only a list is allowed, with each item as: a text.
				 */
				return UText::localize(
					"Only a list is allowed, with each item as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				//scalar
				if ($this->input->isScalar()) {
					/**
					 * @placeholder input.description The input description.
					 * @tags technical
					 * @example Only an array is allowed, \
					 * which may be given as a comma separated list of values or a JSON array, \
					 * with each value as: a string of characters.
					 */
					return UText::localize(
						"Only an array is allowed, " . 
							"which may be given as a comma separated list of values or a JSON array, " . 
							"with each value as: {{input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'input' => ['description' => $input_description]
							]
						]
					);
				}
				
				//default
				/**
				 * @placeholder input.description The input description.
				 * @tags technical
				 * @example Only an array is allowed, with each value as: a string of characters.
				 */
				return UText::localize(
					"Only an array is allowed, with each value as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//scalar
			if ($this->input->isScalar()) {
				/**
				 * @placeholder input.description The input description.
				 * @tags non-technical non-end-user
				 * @example Only a vector is allowed, \
				 * which may be given as a comma separated list of values or a JSON array, \
				 * with each value as: a text.
				 */
				return UText::localize(
					"Only a vector is allowed, " . 
						"which may be given as a comma separated list of values or a JSON array, " . 
						"with each value as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder input.description The input description.
			 * @tags non-technical non-end-user
			 * @example Only a vector is allowed, with each value as: a text.
			 */
			return UText::localize(
				"Only a vector is allowed, with each value as: {{input.description}}",
				self::class, $text_options, [
					'parameters' => [
						'input' => ['description' => $input_description]
					]
				]
			);
		}
		
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize(
				"Only a list is allowed, which may be given as a comma separated list of items.",
				self::class, $text_options
			);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize(
				"Only an array is allowed, which may be given as a comma separated list of values or a JSON array.",
				self::class, $text_options
			);
		}
		
		//non-end-user and non-technical
		/** @tags non-end-user non-technical */
		return UText::localize(
			"Only a vector is allowed, which may be given as a comma separated list of values or a JSON array.",
			self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ErrorMessage)
	/** {@inheritdoc} */
	public function getErrorMessage(TextOptions $text_options): ?string
	{
		//input messages indexes
		$input_messages_indexes = [];
		if (isset($this->input)) {
			foreach ($this->error_values as $i => $value) {
				if (!$this->input->setValue($value, true)) {
					$index = $text_options->info_scope === EInfoScope::ENDUSER ? $i + 1 : $i;
					$input_messages_indexes[$this->input->getErrorMessage($text_options)][] = $index;
				} else {
					$this->input->unsetValue();
				}
			}
		}
		
		//messages
		$messages = [];
		foreach ($input_messages_indexes as $message => $indexes) {
			//initialize
			$input_message = UText::formatMessage($message, true);
			$indexes_string = UText::commify($indexes, $text_options, 'and');
			
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder positions The positions.
				 * @placeholder input.message The input message.
				 * @tags end-user
				 * @example Invalid list items were given at positions 1, 2 and 5, \
				 * with the following error: only text is allowed.
				 */
				$messages[] = UText::plocalize(
					"An invalid list item was given at position {{positions}}, " . 
						"with the following error: {{input.message}}",
					"Invalid list items were given at positions {{positions}}, " . 
						"with the following error: {{input.message}}",
					count($indexes), null, self::class, $text_options, [
						'parameters' => [
							'positions' => $indexes_string,
							'input' => ['message' => $input_message]
						]
					]
				);
				
			//technical
			} elseif ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder indexes The indexes.
				 * @placeholder input.message The input message.
				 * @tags technical
				 * @example Invalid array values were given at indexes 0, 1 and 4, \
				 * with the following error: only a string of characters is allowed.
				 */
				$messages[] = UText::plocalize(
					"An invalid array value was given at index {{indexes}}, " . 
						"with the following error: {{input.message}}",
					"Invalid array values were given at indexes {{indexes}}, " . 
						"with the following error: {{input.message}}",
					count($indexes), null, self::class, $text_options, [
						'parameters' => [
							'indexes' => $indexes_string,
							'input' => ['message' => $input_message]
						]
					]
				);
				
			//non-end-user and non-technical
			} else {
				/**
				 * @placeholder indexes The indexes.
				 * @placeholder input.message The input message.
				 * @tags non-end-user non-technical
				 * @example Invalid vector values were given at indexes 0, 1 and 4, \
				 * with the following error: only text is allowed.
				 */
				$messages[] = UText::plocalize(
					"An invalid vector value was given at index {{indexes}}, " . 
						"with the following error: {{input.message}}",
					"Invalid vector values were given at indexes {{indexes}}, " . 
						"with the following error: {{input.message}}",
					count($indexes), null, self::class, $text_options, [
						'parameters' => [
							'indexes' => $indexes_string,
							'input' => ['message' => $input_message]
						]
					]
				);
			}
		}
		
		//return
		return empty($messages) ? null : implode("\n\n", $messages);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'input' => isset($this->input) ? $this->input->getSchema() : null
		];
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ModifierBuilder)
	/** {@inheritdoc} */
	public function buildModifier(string $name, array $properties): ?Modifier
	{
		switch ($name) {
			//constraints
			case 'constraints.length':
				//no break
			case 'length':
				return $this->createConstraint(Constraints\Length::class, $properties);
			case 'constraints.min_length':
				//no break
			case 'min_length':
				return $this->createConstraint(Constraints\MinLength::class, $properties);
			case 'constraints.max_length':
				//no break
			case 'max_length':
				return $this->createConstraint(Constraints\MaxLength::class, $properties);
			case 'constraints.length_range':
				//no break
			case 'length_range':
				return $this->createConstraint(Constraints\LengthRange::class, $properties);
			case 'constraints.non_empty':
				//no break
			case 'non_empty':
				return $this->createConstraint(Constraints\NonEmpty::class, $properties);
			case 'constraints.unique':
				//no break
			case 'unique':
				return $this->createConstraint(Constraints\Unique::class, $properties);
			
			//filters
			case 'filters.truncate':
				//no break
			case 'truncate':
				return $this->createFilter(Filters\Truncate::class, $properties);
			case 'filters.unique':
				return $this->createFilter(Filters\Unique::class, $properties);
		}
		return null;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ErrorUnset)
	/** {@inheritdoc} */
	public function unsetError(): void
	{
		$this->error_values = [];
		if (isset($this->input)) {
			$this->input->unsetError();
		}
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'input':
				return $this->createProperty()->setMode('w-')->setAsComponent(Component::class)->bind(self::class);
		}
		return null;
	}
}
