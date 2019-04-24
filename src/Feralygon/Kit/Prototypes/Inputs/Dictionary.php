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
 * @property-write \Feralygon\Kit\Components\Input|null $input [writeonce] [default = null]
 * <p>The input instance to evaluate values with.</p>
 * @property-write \Feralygon\Kit\Components\Input|null $key_input [writeonce] [default = null]
 * <p>The input instance to evaluate keys with.</p>
 * @see https://en.wikipedia.org/wiki/Associative_array
 * @see \Feralygon\Kit\Primitives\Dictionary
 * @see \Feralygon\Kit\Interfaces\Arrayable
 */
class Dictionary extends Input implements IInformation, /*IErrorMessage,*/ ISchemaData, IModifierBuilder, IErrorUnset
{
	//Protected properties
	/** @var \Feralygon\Kit\Components\Input|null */
	protected $input = null;
	
	/** @var \Feralygon\Kit\Components\Input|null */
	protected $key_input = null;
	
	/** @var array */
	protected $error_values = [];
	
	/** @var array */
	protected $error_keys = [];
	
	
	
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
		if (isset($this->input) || isset($this->key_input)) {
			//evaluate
			$i = 0;
			$dict = $dictionary->clone()->clear();
			foreach ($dictionary as $k => $v) {
				//initialize
				$has_error = false;
				
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
			if (!empty($this->error_values) || !empty($this->error_keys)) {
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
			$input_label = isset($this->input) ? $this->input->getLabel($text_options, $info_options) : '...'; //TODO: change ... to Any input label
			$key_input_label = $this->key_input->getLabel($text_options, $info_options);
			if (UText::multiline($input_label) || UText::multiline($key_input_label)) {
				$input_label = "\n" . UText::indentate($input_label) . "\n";
				$key_input_label = "\n" . UText::indentate($key_input_label) . "\n" . UText::indentate('');
			}
			
			//label
			/**
			 * @placeholder input.label The input label.
			 * @placeholder key_input.label The key input label.
			 * @example Dictionary<Integer:Text>
			 */
			return UText::localize("Dictionary<{{key_input.label}}:{{input.label}}>", self::class, $text_options, [
				'parameters' => [
					'input' => ['label' => $input_label],
					'key_input' => ['label' => $key_input_label]
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
		
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'input' => isset($this->input) ? $this->input->getSchema() : null,
			'key_input' => isset($this->key_input) ? $this->key_input->getSchema() : null
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
		$this->error_values = $this->error_keys = [];
		
		//inputs
		if (isset($this->input)) {
			$this->input->unsetError();
		}
		if (isset($this->key_input)) {
			$this->key_input->unsetError();
		}
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'input':
				//no break
			case 'key_input':
				return $this->createProperty()->setMode('w-')->setAsComponent(Component::class)->bind(self::class);
		}
		return null;
	}
}
