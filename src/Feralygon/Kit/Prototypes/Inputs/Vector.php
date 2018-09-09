<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs;

use Feralygon\Kit\Prototypes\Input;
use Feralygon\Kit\Prototypes\Input\Interfaces\{
	Information as IInformation,
	SchemaData as ISchemaData,
	ModifierBuilder as IModifierBuilder,
	ErrorUnset as IErrorUnset
};
use Feralygon\Kit\Primitives\Vector as Primitive;
use Feralygon\Kit\Components\Input as Component;
use Feralygon\Kit\Components\Input\Components\Modifier;
/*use Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\{
	Constraints,
	Filters
};*/
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
 * @property-write \Feralygon\Kit\Components\Input|null $input [once] [default = null]
 * <p>The input instance to evaluate values with.</p>
 * @see https://en.wikipedia.org/wiki/Array_data_structure
 * @see https://en.wikipedia.org/wiki/Sequence_container_(C%2B%2B)#Vector
 * @see \Feralygon\Kit\Primitives\Vector
 */
class Vector extends Input implements IInformation, ISchemaData, IModifierBuilder, IErrorUnset
{
	//Private properties
	/** @var \Feralygon\Kit\Components\Input|null */
	private $input = null;
	
	/** @var array */
	private $error_values = [];
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'vector';
	}
	
	/** {@inheritdoc} */
	public function evaluateValue(&$value): bool
	{
		//string
		$vector = $value;
		if (is_string($vector)) {
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
			
			//other
			/**
			 * @placeholder input.label The input label.
			 * @tags non-technical non-end-user
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
		
		//other
		/** @tags non-technical non-end-user */
		return UText::localize("Vector", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		//input
		$input_description = isset($this->input) ? $this->input->getDescription($text_options, $info_options) : null;
		if (isset($input_description)) {
			//description
			$input_description = UText::uncapitalize($input_description, true);
			
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder input.description The input description.
				 * @tags end-user
				 * @example A list of values, with each one as: a text.
				 */
				return UText::localize(
					"A list of values, with each one as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder input.description The input description.
				 * @tags technical
				 * @example An array of values, with each one as: a string of characters.
				 */
				return UText::localize(
					"An array of values, with each one as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//other
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
			return UText::localize("A list of values.", self::class, $text_options);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("An array of values.", self::class, $text_options);
		}
		
		//other
		/** @tags non-technical non-end-user */
		return UText::localize("A vector.", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		//input
		$input_description = isset($this->input) ? $this->input->getDescription($text_options, $info_options) : null;
		if (isset($input_description)) {
			//description
			$input_description = UText::uncapitalize($input_description, true);
			
			//end-user
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/**
				 * @placeholder input.description The input description.
				 * @tags end-user
				 * @example Only a list of values is allowed, with each one as: a text.
				 */
				return UText::localize(
					"Only a list of values is allowed, with each one as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//technical
			if ($text_options->info_scope === EInfoScope::TECHNICAL) {
				/**
				 * @placeholder input.description The input description.
				 * @tags technical
				 * @example Only an array of values is allowed, with each one as: a string of characters.
				 */
				return UText::localize(
					"Only an array of values is allowed, with each one as: {{input.description}}",
					self::class, $text_options, [
						'parameters' => [
							'input' => ['description' => $input_description]
						]
					]
				);
			}
			
			//other
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
			return UText::localize("Only a list of values is allowed.", self::class, $text_options);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("Only an array of values is allowed.", self::class, $text_options);
		}
		
		//other
		/** @tags non-technical non-end-user */
		return UText::localize("Only a vector is allowed.", self::class, $text_options);
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
		$this->error_values = [];
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
