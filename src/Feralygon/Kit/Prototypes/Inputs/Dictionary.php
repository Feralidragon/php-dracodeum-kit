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
/*use Feralygon\Kit\Prototypes\Inputs\Dictionary\Prototypes\Modifiers\{
	Constraints,
	Filters
};*/
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
 * &nbsp; &#8226; &nbsp; a string as a comma separated list of key-value pairs, 
 * such as <samp>key1:value1,key2:value2,key3:value3</samp>;<br>
 * &nbsp; &#8226; &nbsp; a JSON array.
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
class Dictionary extends Input implements IInformation, IErrorMessage, ISchemaData, IModifierBuilder, IErrorUnset
{
	//Protected properties
	/** @var \Feralygon\Kit\Components\Input|null */
	protected $input = null;
	
	/** @var \Feralygon\Kit\Components\Input|null */
	protected $key_input = null;
	
	/** @var array */
	protected $error_pairs = [];
	
	
	
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
		
		//TODO
		
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		
		//TODO
		
	}
	
	/** {@inheritdoc} */
	public function getDescription(TextOptions $text_options, InfoOptions $info_options): string
	{
		
		//TODO
		
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options, InfoOptions $info_options): string
	{
		
		//TODO
		
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
		$this->error_pairs = [];
		
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
