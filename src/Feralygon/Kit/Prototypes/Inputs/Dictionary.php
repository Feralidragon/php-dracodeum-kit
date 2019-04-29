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
//use Feralygon\Kit\Prototypes\Inputs\Dictionary\Prototypes\Modifiers\Constraints;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;
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
 * @property-write \Feralygon\Kit\Components\Input|null $key_input [writeonce] [default = null]
 * <p>The input instance to evaluate keys with.</p>
 * @property-write \Feralygon\Kit\Components\Input|null $input [writeonce] [default = null]
 * <p>The input instance to evaluate values with.</p>
 * @see https://en.wikipedia.org/wiki/Associative_array
 * @see \Feralygon\Kit\Primitives\Dictionary
 * @see \Feralygon\Kit\Interfaces\Arrayable
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
			} elseif (preg_match('/^[^:,]*:[^:,]*(?:,[^:,]*:[^:,]*)*$/', $dictionary)) {
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
			$input_label = isset($this->input) ? $this->input->getLabel($text_options, $info_options) : '...'; //TODO: change ... to Any input label
			if (UText::multiline($key_input_label) || UText::multiline($input_label)) {
				$key_input_label = "\n" . UText::indentate($key_input_label) . "\n" . UText::indentate('');
				$input_label = "\n" . UText::indentate($input_label) . "\n";
			}
			
			//label
			/**
			 * @placeholder key_input.label The key input label.
			 * @placeholder input.label The input label.
			 * @example Dictionary<Integer:Text>
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
		//input description
		$input_description = isset($this->input) ? $this->input->getDescription($text_options, $info_options) : null;
		if (isset($input_description)) {
			$input_description = UText::formatMessage($input_description, true);
		}
		
		//key input description
		$key_input_description = isset($this->key_input)
			? $this->key_input->getDescription($text_options, $info_options)
			: null;
		if (isset($key_input_description)) {
			$key_input_description = UText::formatMessage($key_input_description, true);
		}
		
		//inputs
		if (isset($input_description) && isset($key_input_description)) {
			//key input description
			/**
			 * @description Key bullet point with input description.
			 * @placeholder key_input.description The key input description.
			 * @example with each key as: a number.
			 */
			$key_input_description = UText::localize(
				"with each key as: {{key_input.description}}",
				self::class, $text_options, [
					'parameters' => [
						'key_input' => ['description' => $key_input_description]
					]
				]
			);
			if (UText::multiline($key_input_description)) {
				$key_input_description .= "\n";
			}
			$key_input_description = UText::bulletify($key_input_description, $text_options);
			
			//input description
			/**
			 * @description Value bullet point with input description.
			 * @placeholder input.description The input description.
			 * @example with each value as: a text.
			 */
			$input_description = UText::localize(
				"with each value as: {{input.description}}",
				self::class, $text_options, [
					'parameters' => [
						'input' => ['description' => $input_description]
					]
				]
			);
			if (UText::multiline($input_description)) {
				$input_description .= "\n";
			}
			$input_description = UText::bulletify($input_description, $text_options);
			
			//inputs descriptions
			$inputs_descriptions = "{$key_input_description}\n{$input_description}";
			
			//scalar
			if ($this->key_input->isScalar() && $this->input->isScalar()) {
				/**
				 * @placeholder key_input.description The key input description.
				 * @placeholder input.description The input description.
				 * @example A dictionary, \
				 * which may be given as a comma separated list of colon separated key-value pairs:
				 *  &#8226; with each key as: a number.
				 *  &#8226; with each value as: a text.
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
			
			//default
			/**
			 * @placeholder inputs_descriptions The inputs descriptions.
			 * @example A dictionary:
			 *  &#8226; with each key as: a number.
			 *  &#8226; with each value as: a text.
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
		
		
		//TODO
		return '?';
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		
		//TODO
		return '?';
		
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\ErrorMessage)
	/** {@inheritdoc} */
	public function getErrorMessage(TextOptions $text_options): ?string
	{
		
		//TODO
		return null;
		
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
	public function buildModifier(string $name, array $properties = []): ?Modifier
	{
		switch ($name) {
			
			//TODO
			
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
}
