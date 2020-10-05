<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs;

use Dracodeum\Kit\Prototypes\Input;
use Dracodeum\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	ErrorMessage as IErrorMessage,
	SchemaData as ISchemaData,
	ConstraintProducer as IConstraintProducer,
	ErrorUnsetter as IErrorUnsetter
};
use Dracodeum\Kit\Primitives\Dictionary as Primitive;
use Dracodeum\Kit\Components\Input as Component;
use Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Components\Input\Options\Info as InfoOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This input prototype represents a dictionary, as an instance of <code>Dracodeum\Kit\Primitives\Dictionary</code>.
 * 
 * Only the following types of values may be evaluated as a dictionary:<br>
 * &nbsp; &#8226; &nbsp; a <code>Dracodeum\Kit\Primitives\Dictionary</code> instance;<br>
 * &nbsp; &#8226; &nbsp; an associative array;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface;<br>
 * &nbsp; &#8226; &nbsp; a string as a comma separated list of colon separated key-value pairs, 
 * such as <samp>key1:value1,key2:value2,key3:value3</samp>;<br>
 * &nbsp; &#8226; &nbsp; a JSON array or object.
 * 
 * @property-write \Dracodeum\Kit\Components\Input|null $key_input [writeonce] [transient] [coercive] [default = null]
 * <p>The input instance to evaluate keys with.</p>
 * @property-write \Dracodeum\Kit\Components\Input|null $input [writeonce] [transient] [coercive] [default = null]
 * <p>The input instance to evaluate values with.</p>
 * @see https://en.wikipedia.org/wiki/Associative_array
 * @see \Dracodeum\Kit\Primitives\Dictionary
 * @see \Dracodeum\Kit\Interfaces\Arrayable
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints\Length
 * [constraint, name = 'length']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints\MinLength
 * [constraint, name = 'min_length']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints\MaxLength
 * [constraint, name = 'max_length']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints\LengthRange
 * [constraint, name = 'length_range']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints\NonEmpty
 * [constraint, name = 'non_empty']
 * @see \Dracodeum\Kit\Prototypes\Inputs\Dictionary\Constraints\Unique
 * [constraint, name = 'unique']
 */
class Dictionary extends Input implements IInformation, IErrorMessage, ISchemaData, IConstraintProducer, IErrorUnsetter
{
	//Protected properties
	/** @var \Dracodeum\Kit\Components\Input|null */
	protected $key_input = null;
	
	/** @var \Dracodeum\Kit\Components\Input|null */
	protected $input = null;
	
	/** @var array */
	protected $error_keys = [];
	
	/** @var array */
	protected $error_values = [];
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'dictionary';
	}
	
	/** {@inheritdoc} */
	public function isScalar(): bool
	{
		return false;
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		//string
		$dictionary = $value;
		if (is_string($dictionary)) {
			$dictionary = trim($dictionary);
			if ($dictionary === '') {
				$dictionary = [];
			} elseif ($dictionary[0] === '[' || $dictionary[0] === '{') {
				$dictionary = json_decode($dictionary, true);
				if (!isset($dictionary)) {
					return false;
				}
			} elseif (preg_match('/^[^:,]*:[^:,]*(?:,[^:,]*:[^:,]*)*$/sm', $dictionary)) {
				preg_match_all('/(?P<keys>[^:,]*):(?P<values>[^:,]*)/', $dictionary, $matches);
				$dictionary = array_combine(array_map('trim', $matches['keys']), array_map('trim', $matches['values']));
			} else {
				return false;
			}
		}
		
		//evaluate
		if (!Primitive::evaluate($dictionary)) {
			return false;
		}
		
		//inputs
		if (isset($this->key_input) || isset($this->input)) {
			//evaluate
			$i = 0;
			$dict = $dictionary->clone()->clear();
			foreach ($dictionary as $k => $v) {
				//initialize
				$has_error = false;
				
				//key input
				if (isset($this->key_input)) {
					if ($this->key_input->setValue($k, true)) {
						$k = $this->key_input->getValue();
						$this->key_input->unsetValue();
					} else {
						$this->error_keys[$i] = $k;
						$has_error = true;
					}
				}
				
				//input
				if (isset($this->input)) {
					if ($this->input->setValue($v, true)) {
						$v = $this->input->getValue();
						$this->input->unsetValue();
					} else {
						$this->error_values[$i] = $v;
						$has_error = true;
					}
				}
				
				//set
				if (!$has_error) {
					$dict->set($k, $v);
				}
				
				//finalize
				$i++;
			}
			$dictionary = $dict;
			unset($dict);
			
			//check
			if (!empty($this->error_keys) || !empty($this->error_values)) {
				return false;
			}
		}
		
		//finalize
		$value = $dictionary;
		return true;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		//key input
		if (isset($this->key_input)) {
			//input labels
			$key_input_label = $this->key_input->getLabel($text_options, $info_options);
			$input_label = isset($this->input) ? $this->input->getLabel($text_options, $info_options) : '...';
			if (UText::multiline($key_input_label) || UText::multiline($input_label)) {
				$key_input_label = "\n" . UText::indentate($key_input_label) . "\n" . UText::indentate('');
				$input_label = "\n" . UText::indentate($input_label) . "\n";
			}
			
			//label
			/**
			 * @placeholder key_input.label The key input label.
			 * @placeholder input.label The input label.
			 * @example Dictionary<Text:Number>
			 */
			return UText::localize("Dictionary<{{key_input.label}}:{{input.label}}>", self::class, $text_options, [
				'parameters' => [
					'key_input' => ['label' => $key_input_label],
					'input' => ['label' => $input_label]
				]
			]);
		}
		
		//input
		if (isset($this->input)) {
			//input label
			$input_label = $this->input->getLabel($text_options, $info_options);
			if (UText::multiline($input_label)) {
				$input_label = "\n" . UText::indentate($input_label) . "\n";
			}
			
			//label
			/**
			 * @placeholder input.label The input label.
			 * @example Dictionary<Text>
			 */
			return UText::localize("Dictionary<{{input.label}}>", self::class, $text_options, [
				'parameters' => [
					'input' => ['label' => $input_label]
				]
			]);
		}
		
		//default
		return UText::localize("Dictionary", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//descriptions
		$key_input_description = $this->getFormattedKeyInputDescription($text_options, $info_options);
		$input_description = $this->getFormattedInputDescription($text_options, $info_options);
		
		//inputs
		if (isset($key_input_description) && isset($input_description)) {
			//inputs descriptions
			$inputs_descriptions = UText::mbulletify([
				$this->getKeyDescriptionBulletPoint($key_input_description, $text_options),
				$this->getValueDescriptionBulletPoint($input_description, $text_options)
			], $text_options, [
				'merge' => true,
				'multiline_newline_append' => true
			]);
			
			//scalar
			if ($this->key_input->isScalar() && $this->input->isScalar()) {
				//end-user
				if ($text_options->info_scope === EInfoScope::ENDUSER) {
					/**
					 * @placeholder inputs_descriptions The inputs descriptions.
					 * @tags end-user
					 * @example A dictionary, \
					 * which may be given as a comma separated list of colon separated key-value pairs:
					 *  &#8226; with each key as: a text.
					 *  &#8226; with each value as: a number.
					 */
					return UText::localize(
						"A dictionary, " . 
							"which may be given as a comma separated list of colon separated key-value pairs:\n" . 
							"{{inputs_descriptions}}",
						self::class, $text_options, [
							'parameters' => [
								'inputs_descriptions' => $inputs_descriptions
							]
						]
					);
				}
				
				//non-end-user
				/**
				 * @placeholder inputs_descriptions The inputs descriptions.
				 * @tags non-end-user
				 * @example A dictionary, \
				 * which may be given as a comma separated list of colon separated key-value pairs, \
				 * or a JSON array or object:
				 *  &#8226; with each key as: a string of characters.
				 *  &#8226; with each value as: a number.
				 */
				return UText::localize(
					"A dictionary, " . 
						"which may be given as a comma separated list of colon separated key-value pairs, " . 
						"or a JSON array or object:\n{{inputs_descriptions}}",
					self::class, $text_options, [
						'parameters' => [
							'inputs_descriptions' => $inputs_descriptions
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder inputs_descriptions The inputs descriptions.
			 * @example A dictionary:
			 *  &#8226; with each key as: a text.
			 *  &#8226; with each value as: a number.
			 */
			return UText::localize(
				"A dictionary:\n{{inputs_descriptions}}",
				self::class, $text_options, [
					'parameters' => [
						'inputs_descriptions' => $inputs_descriptions
					]
				]
			);
		}
		
		//key input
		if (isset($key_input_description)) {
			//scalar
			if ($this->key_input->isScalar()) {
				//end-user
				if ($text_options->info_scope === EInfoScope::ENDUSER) {
					/**
					 * @placeholder key_input.description The key input description.
					 * @tags end-user
					 * @example A dictionary, \
					 * which may be given as a comma separated list of colon separated key-value pairs, \
					 * with each key as: a text.
					 */
					return UText::localize(
						"A dictionary, " . 
							"which may be given as a comma separated list of colon separated key-value pairs, " . 
							"with each key as: {{key_input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'key_input' => ['description' => $key_input_description]
							]
						]
					);
				}
				
				//non-end-user
				/**
				 * @placeholder key_input.description The key input description.
				 * @tags non-end-user
				 * @example A dictionary, \
				 * which may be given as a comma separated list of colon separated key-value pairs, \
				 * or a JSON array or object, with each key as: a string of characters.
				 */
				return UText::localize(
					"A dictionary, " . 
						"which may be given as a comma separated list of colon separated key-value pairs, " . 
						"or a JSON array or object, with each key as: {{key_input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'key_input' => ['description' => $key_input_description]
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder key_input.description The key input description.
			 * @example A dictionary, with each key as: a text.
			 */
			return UText::localize(
				"A dictionary, with each key as: {{key_input.description}}",
				self::class, $text_options, [
					'parameters' => [
						'key_input' => ['description' => $key_input_description]
					]
				]
			);
		}
		
		//input
		if (isset($input_description)) {
			//scalar
			if ($this->input->isScalar()) {
				//end-user
				if ($text_options->info_scope === EInfoScope::ENDUSER) {
					/**
					 * @placeholder input.description The input description.
					 * @tags end-user
					 * @example A dictionary, \
					 * which may be given as a comma separated list of colon separated key-value pairs, \
					 * with each value as: a number.
					 */
					return UText::localize(
						"A dictionary, " . 
							"which may be given as a comma separated list of colon separated key-value pairs, " . 
							"with each value as: {{input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'input' => ['description' => $input_description]
							]
						]
					);
				}
				
				//non-end-user
				/**
				 * @placeholder input.description The input description.
				 * @tags non-end-user
				 * @example A dictionary, \
				 * which may be given as a comma separated list of colon separated key-value pairs, \
				 * or a JSON array or object, with each value as: a number.
				 */
				return UText::localize(
					"A dictionary, " . 
						"which may be given as a comma separated list of colon separated key-value pairs, " . 
						"or a JSON array or object, with each value as: {{input.description}}",
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
			 * @example A dictionary, with each value as: a number.
			 */
			return UText::localize(
				"A dictionary, with each value as: {{input.description}}",
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
				"A dictionary, which may be given as a comma separated list of colon separated key-value pairs.",
				self::class, $text_options
			);
		}
		
		//non-end-user
		/** @tags non-end-user */
		return UText::localize(
			"A dictionary, which may be given as a comma separated list of colon separated key-value pairs, " . 
				"or a JSON array or object.",
			self::class, $text_options
		);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		//descriptions
		$key_input_description = $this->getFormattedKeyInputDescription($text_options, $info_options);
		$input_description = $this->getFormattedInputDescription($text_options, $info_options);
		
		//inputs
		if (isset($key_input_description) && isset($input_description)) {
			//inputs descriptions
			$inputs_descriptions = UText::mbulletify([
				$this->getKeyDescriptionBulletPoint($key_input_description, $text_options),
				$this->getValueDescriptionBulletPoint($input_description, $text_options)
			], $text_options, [
				'merge' => true,
				'multiline_newline_append' => true
			]);
			
			//scalar
			if ($this->key_input->isScalar() && $this->input->isScalar()) {
				//end-user
				if ($text_options->info_scope === EInfoScope::ENDUSER) {
					/**
					 * @placeholder inputs_descriptions The inputs descriptions.
					 * @tags end-user
					 * @example Only a dictionary is allowed, \
					 * which may be given as a comma separated list of colon separated key-value pairs:
					 *  &#8226; with each key as: a text.
					 *  &#8226; with each value as: a number.
					 */
					return UText::localize(
						"Only a dictionary is allowed, " . 
							"which may be given as a comma separated list of colon separated key-value pairs:\n" . 
							"{{inputs_descriptions}}",
						self::class, $text_options, [
							'parameters' => [
								'inputs_descriptions' => $inputs_descriptions
							]
						]
					);
				}
				
				//non-end-user
				/**
				 * @placeholder inputs_descriptions The inputs descriptions.
				 * @tags non-end-user
				 * @example Only a dictionary is allowed, \
				 * which may be given as a comma separated list of colon separated key-value pairs, \
				 * or a JSON array or object:
				 *  &#8226; with each key as: a string of characters.
				 *  &#8226; with each value as: a number.
				 */
				return UText::localize(
					"Only a dictionary is allowed, " . 
						"which may be given as a comma separated list of colon separated key-value pairs, " . 
						"or a JSON array or object:\n{{inputs_descriptions}}",
					self::class, $text_options, [
						'parameters' => [
							'inputs_descriptions' => $inputs_descriptions
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder inputs_descriptions The inputs descriptions.
			 * @example Only a dictionary is allowed:
			 *  &#8226; with each key as: a text.
			 *  &#8226; with each value as: a number.
			 */
			return UText::localize(
				"Only a dictionary is allowed:\n{{inputs_descriptions}}",
				self::class, $text_options, [
					'parameters' => [
						'inputs_descriptions' => $inputs_descriptions
					]
				]
			);
		}
		
		//key input
		if (isset($key_input_description)) {
			//scalar
			if ($this->key_input->isScalar()) {
				//end-user
				if ($text_options->info_scope === EInfoScope::ENDUSER) {
					/**
					 * @placeholder key_input.description The key input description.
					 * @tags end-user
					 * @example Only a dictionary is allowed, \
					 * which may be given as a comma separated list of colon separated key-value pairs, \
					 * with each key as: a text.
					 */
					return UText::localize(
						"Only a dictionary is allowed, " . 
							"which may be given as a comma separated list of colon separated key-value pairs, " . 
							"with each key as: {{key_input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'key_input' => ['description' => $key_input_description]
							]
						]
					);
				}
				
				//non-end-user
				/**
				 * @placeholder key_input.description The key input description.
				 * @tags non-end-user
				 * @example Only a dictionary is allowed, \
				 * which may be given as a comma separated list of colon separated key-value pairs, \
				 * or a JSON array or object, with each key as: a string of characters.
				 */
				return UText::localize(
					"Only a dictionary is allowed, " . 
						"which may be given as a comma separated list of colon separated key-value pairs, " . 
						"or a JSON array or object, with each key as: {{key_input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'key_input' => ['description' => $key_input_description]
						]
					]
				);
			}
			
			//default
			/**
			 * @placeholder key_input.description The key input description.
			 * @example Only a dictionary is allowed, with each key as: a text.
			 */
			return UText::localize(
				"Only a dictionary is allowed, with each key as: {{key_input.description}}",
				self::class, $text_options, [
					'parameters' => [
						'key_input' => ['description' => $key_input_description]
					]
				]
			);
		}
		
		//input
		if (isset($input_description)) {
			//scalar
			if ($this->input->isScalar()) {
				//end-user
				if ($text_options->info_scope === EInfoScope::ENDUSER) {
					/**
					 * @placeholder input.description The input description.
					 * @tags end-user
					 * @example Only a dictionary is allowed, \
					 * which may be given as a comma separated list of colon separated key-value pairs, \
					 * with each value as: a number.
					 */
					return UText::localize(
						"Only a dictionary is allowed, " . 
							"which may be given as a comma separated list of colon separated key-value pairs, " . 
							"with each value as: {{input.description}}",
						self::class, $text_options, [
							'parameters' => [
								'input' => ['description' => $input_description]
							]
						]
					);
				}
				
				//non-end-user
				/**
				 * @placeholder input.description The input description.
				 * @tags non-end-user
				 * @example Only a dictionary is allowed, \
				 * which may be given as a comma separated list of colon separated key-value pairs, \
				 * or a JSON array or object, with each value as: a number.
				 */
				return UText::localize(
					"Only a dictionary is allowed, " . 
						"which may be given as a comma separated list of colon separated key-value pairs, " . 
						"or a JSON array or object, with each value as: {{input.description}}",
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
			 * @example Only a dictionary is allowed, with each value as: a number.
			 */
			return UText::localize(
				"Only a dictionary is allowed, with each value as: {{input.description}}",
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
				"Only a dictionary is allowed, " . 
					"which may be given as a comma separated list of colon separated key-value pairs.",
				self::class, $text_options
			);
		}
		
		//non-end-user
		/** @tags non-end-user */
		return UText::localize(
			"Only a dictionary is allowed, " . 
				"which may be given as a comma separated list of colon separated key-value pairs, " . 
				"or a JSON array or object.",
			self::class, $text_options
		);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ErrorMessage)
	/** {@inheritdoc} */
	public function getErrorMessage(TextOptions $text_options): ?string
	{
		//key input messages positions
		$key_input_messages_positions = [];
		if (isset($this->key_input)) {
			foreach ($this->error_keys as $i => $key) {
				if (!$this->key_input->setValue($key, true)) {
					$key_input_messages_positions[$this->key_input->getErrorMessage($text_options)][] = $i + 1;
				} else {
					$this->key_input->unsetValue();
				}
			}
		}
		
		//input messages positions
		$input_messages_positions = [];
		if (isset($this->input)) {
			foreach ($this->error_values as $i => $value) {
				if (!$this->input->setValue($value, true)) {
					$input_messages_positions[$this->input->getErrorMessage($text_options)][] = $i + 1;
				} else {
					$this->input->unsetValue();
				}
			}
		}
		
		//messages (initialize)
		$messages = [];
		
		//messages (key input)
		foreach ($key_input_messages_positions as $message => $positions) {
			/**
			 * @placeholder positions The positions.
			 * @placeholder key_input.message The key input message.
			 * @example Invalid dictionary keys were given at positions 1, 2 and 5, \
			 * with the following error: only text is allowed.
			 */
			$messages[] = UText::plocalize(
				"An invalid dictionary key was given at position {{positions}}, " . 
					"with the following error: {{key_input.message}}",
				"Invalid dictionary keys were given at positions {{positions}}, " . 
					"with the following error: {{key_input.message}}",
				count($positions), null, self::class, $text_options, [
					'parameters' => [
						'positions' => UText::commify($positions, $text_options, 'and'),
						'key_input' => ['message' => UText::formatMessage($message, true)]
					]
				]
			);
		}
		
		//messages (input)
		foreach ($input_messages_positions as $message => $positions) {
			/**
			 * @placeholder positions The positions.
			 * @placeholder input.message The input message.
			 * @example Invalid dictionary values were given at positions 1, 2 and 5, \
			 * with the following error: only a number is allowed.
			 */
			$messages[] = UText::plocalize(
				"An invalid dictionary value was given at position {{positions}}, " . 
					"with the following error: {{input.message}}",
				"Invalid dictionary values were given at positions {{positions}}, " . 
					"with the following error: {{input.message}}",
				count($positions), null, self::class, $text_options, [
					'parameters' => [
						'positions' => UText::commify($positions, $text_options, 'and'),
						'input' => ['message' => UText::formatMessage($message, true)]
					]
				]
			);
		}
		
		//return
		return empty($messages) ? null : implode("\n\n", $messages);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'key_input' => isset($this->key_input) ? $this->key_input->getSchema() : null,
			'input' => isset($this->input) ? $this->input->getSchema() : null
		];
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ConstraintProducer)
	/** {@inheritdoc} */
	public function produceConstraint(string $name, array $properties)
	{
		switch ($name) {
			case 'length':
				return Constraints\Length::class;
			case 'min_length':
				return Constraints\MinLength::class;
			case 'max_length':
				return Constraints\MaxLength::class;
			case 'length_range':
				return Constraints\LengthRange::class;
			case 'non_empty':
				return Constraints\NonEmpty::class;
			case 'unique':
				return Constraints\Unique::class;
		}
		return null;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Prototypes\Input\Interfaces\ErrorUnset)
	/** {@inheritdoc} */
	public function unsetError(): void
	{
		//errors
		$this->error_keys = $this->error_values = [];
		
		//inputs
		if (isset($this->key_input)) {
			$this->key_input->unsetError();
		}
		if (isset($this->input)) {
			$this->input->unsetError();
		}
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'key_input':
				//no break
			case 'input':
				return $this->createProperty()->setMode('w--')->setAsComponent(Component::class)->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get formatted key input description.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info $info_options
	 * <p>The info options instance to use.</p>
	 * @return string|null
	 * <p>The formatted key input description or <code>null</code> if none is set.</p>
	 */
	protected function getFormattedKeyInputDescription(TextOptions $text_options, InfoOptions $info_options): ?string
	{
		$description = isset($this->key_input) ? $this->key_input->getDescription($text_options, $info_options) : null;
		if (isset($description)) {
			$description = UText::formatMessage($description, true);
		}
		return $description;
	}
	
	/**
	 * Get formatted input description.
	 * 
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Dracodeum\Kit\Components\Input\Options\Info $info_options
	 * <p>The info options instance to use.</p>
	 * @return string|null
	 * <p>The formatted input description or <code>null</code> if none is set.</p>
	 */
	protected function getFormattedInputDescription(TextOptions $text_options, InfoOptions $info_options): ?string
	{
		$description = isset($this->input) ? $this->input->getDescription($text_options, $info_options) : null;
		if (isset($description)) {
			$description = UText::formatMessage($description, true);
		}
		return $description;
	}
	
	/**
	 * Get key description bullet point with a given description.
	 * 
	 * @param string $description
	 * <p>The description to get with.</p>
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The key description bullet point with the given description.</p>
	 */
	protected function getKeyDescriptionBulletPoint(string $description, TextOptions $text_options): string
	{
		/**
		 * @description Bullet point with key description.
		 * @placeholder key_input.description The key input description.
		 * @example with each key as: a number.
		 */
		return UText::localize(
			"with each key as: {{key_input.description}}",
			self::class, $text_options, [
				'parameters' => [
					'key_input' => ['description' => $description]
				]
			]
		);
	}
	
	/**
	 * Get value description bullet point with a given description.
	 * 
	 * @param string $description
	 * <p>The description to get with.</p>
	 * @param \Dracodeum\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @return string
	 * <p>The value description bullet point with the given description.</p>
	 */
	protected function getValueDescriptionBulletPoint(string $description, TextOptions $text_options): string
	{
		/**
		 * @description Bullet point with value description.
		 * @placeholder input.description The input description.
		 * @example with each value as: a text.
		 */
		return UText::localize(
			"with each value as: {{input.description}}",
			self::class, $text_options, [
				'parameters' => [
					'input' => ['description' => $description]
				]
			]
		);
	}
}
