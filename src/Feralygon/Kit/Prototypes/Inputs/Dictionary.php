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
use Feralygon\Kit\Primitives\Dictionary as Primitive;
use Feralygon\Kit\Components\Input as Component;
use Feralygon\Kit\Components\Input\Components\Modifier;
use Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This input prototype represents a dictionary, as an instance of <code>Feralygon\Kit\Primitives\Dictionary</code>.
 * 
 * Only the following types of values may be evaluated as a dictionary:<br>
 * &nbsp; &#8226; &nbsp; a <code>Feralygon\Kit\Primitives\Dictionary</code> instance;<br>
 * &nbsp; &#8226; &nbsp; an associative array;<br>
 * &nbsp; &#8226; &nbsp; an object implementing the <code>Feralygon\Kit\Interfaces\Arrayable</code> interface;<br>
 * &nbsp; &#8226; &nbsp; a string as a comma separated list of colon separated key-value pairs, 
 * such as <samp>key1:value1,key2:value2,key3:value3</samp>;<br>
 * &nbsp; &#8226; &nbsp; a JSON array or object.
 * 
 * @since 1.0.0
 * @property-write \Feralygon\Kit\Components\Input|null $key_input [writeonce] [coercive = component] [default = null]
 * <p>The input instance to evaluate keys with.</p>
 * @property-write \Feralygon\Kit\Components\Input|null $input [writeonce] [coercive = component] [default = null]
 * <p>The input instance to evaluate values with.</p>
 * @see https://en.wikipedia.org/wiki/Associative_array
 * @see \Feralygon\Kit\Primitives\Dictionary
 * @see \Feralygon\Kit\Interfaces\Arrayable
 * @see \Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints\Length
 * [modifier, name = 'constraints.length' or 'length']
 * @see \Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints\MinLength
 * [modifier, name = 'constraints.min_length' or 'min_length']
 * @see \Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints\MaxLength
 * [modifier, name = 'constraints.max_length' or 'max_length']
 * @see \Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints\LengthRange
 * [modifier, name = 'constraints.length_range' or 'length_range']
 * @see \Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints\NonEmpty
 * [modifier, name = 'constraints.non_empty' or 'non_empty']
 * @see \Feralygon\Kit\Prototypes\Inputs\Dictionary\Constraints\Unique
 * [modifier, name = 'constraints.unique' or 'unique']
 */
class Dictionary extends Input implements IInformation, IErrorMessage, ISchemaData, IModifierBuilder, IErrorUnset
{
	//Protected properties
	/** @var \Feralygon\Kit\Components\Input|null */
	protected $key_input = null;
	
	/** @var \Feralygon\Kit\Components\Input|null */
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
				
				//finish
				$i++;
			}
			$dictionary = $dict;
			unset($dict);
			
			//check
			if (!empty($this->error_keys) || !empty($this->error_values)) {
				return false;
			}
		}
		
		//finish
		$value = $dictionary;
		return true;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ErrorMessage)
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
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'key_input' => isset($this->key_input) ? $this->key_input->getSchema() : null,
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
		}
		return null;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ErrorUnset)
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'key_input':
				//no break
			case 'input':
				return $this->createProperty()->setMode('w-')->setAsComponent(Component::class)->bind(self::class);
		}
		return null;
	}
	
	
	
	//Protected methods
	/**
	 * Get formatted key input description.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info $info_options
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
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Options\Text $text_options
	 * <p>The text options instance to use.</p>
	 * @param \Feralygon\Kit\Components\Input\Options\Info $info_options
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
	 * @since 1.0.0
	 * @param string $description
	 * <p>The description to get with.</p>
	 * @param \Feralygon\Kit\Options\Text $text_options
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
	 * @since 1.0.0
	 * @param string $description
	 * <p>The description to get with.</p>
	 * @param \Feralygon\Kit\Options\Text $text_options
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
