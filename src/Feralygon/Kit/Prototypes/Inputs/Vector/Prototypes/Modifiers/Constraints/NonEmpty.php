<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Vector\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation
};
use Feralygon\Kit\Primitives\Vector as Primitive;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This constraint prototype prevents a vector from being empty.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Vector
 */
class NonEmpty extends Constraint implements IName, IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value): bool
	{
		return is_object($value) && $value instanceof Primitive ? !$value->isEmpty() : false;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'constraints.non_empty';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Components\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options): string
	{
		return UText::localize("Non-empty", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options): string
	{
		//end-user
		if ($text_options->info_scope === EInfoScope::ENDUSER) {
			/** @tags end-user */
			return UText::localize("An empty list is not allowed.", self::class, $text_options);
		}
		
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("An empty array is not allowed.", self::class, $text_options);
		}
		
		//other
		/** @tags non-end-user non-technical */
		return UText::localize("An empty vector is not allowed.", self::class, $text_options);
	}
}