<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Inputs\Text\Constraints;

use Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Subtype as ISubtype,
	Information as IInformation,
	SchemaData as ISchemaData
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * This constraint prototype prevents a given text input value from being empty.
 * 
 * @property-write bool $ignore_whitespace [writeonce] [transient] [default = false]
 * <p>Ignore whitespace characters from a given text input value.</p>
 */
class NonEmpty extends Constraint implements ISubtype, IInformation, ISchemaData
{
	//Protected properties
	/** @var bool */
	protected $ignore_whitespace = false;
	
	
	
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'non_empty';
	}
	
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return UType::evaluateString($value) && !UText::empty($this->ignore_whitespace ? trim($value) : $value);
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Subtype)
	/** {@inheritdoc} */
	public function getSubtype(): string
	{
		return 'text';
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Non-empty", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//message
		$message = '';
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			$message = UText::localize("An empty string is not allowed.", self::class, $text_options);
		} else {
			/** @tags non-technical */
			$message = UText::localize("An empty text is not allowed.", self::class, $text_options);
		}
		
		//ignore whitespace
		if ($this->ignore_whitespace) {
			$message .= "\n";
			if ($text_options->info_scope === EInfoScope::ENDUSER) {
				/** @tags end-user */
				$message .= UText::localize(
					"All space, tab and newline characters are ignored.", self::class, $text_options
				);
			} else {
				/** @tags non-end-user */
				$message .= UText::localize(
					"All whitespace characters are ignored.", self::class, $text_options
				);
			}
		}
		
		//return
		return $message;
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Components\Input\Prototypes\Modifier\Interfaces\SchemaData)
	/** {@inheritdoc} */
	public function getSchemaData()
	{
		return [
			'ignore_whitespace' => $this->ignore_whitespace
		];
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Prototype\Traits\PropertyBuilder)
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'ignore_whitespace':
				return $this->createProperty()->setMode('w--')->setAsBoolean()->bind(self::class);
		}
		return null;
	}
}
