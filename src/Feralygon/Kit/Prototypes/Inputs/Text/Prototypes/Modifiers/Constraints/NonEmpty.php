<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Text\Prototypes\Modifiers\Constraints;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Constraint;
use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\{
	Name as IName,
	Information as IInformation
};
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This constraint prototype prevents a text or string from being empty.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Text
 */
class NonEmpty extends Constraint implements IName, IInformation
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function checkValue($value) : bool
	{
		return !UText::empty($value);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Name)
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'constraints.non_empty';
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Prototypes\Input\Prototypes\Modifier\Interfaces\Information)
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options) : string
	{
		return UText::localize("Non-empty", self::class, $text_options);
	}
	
	/** {@inheritdoc} */
	public function getMessage(TextOptions $text_options) : string
	{
		//technical
		if ($text_options->info_scope === EInfoScope::TECHNICAL) {
			/** @tags technical */
			return UText::localize("An empty string is not allowed.", self::class, $text_options);
		}
		
		//non-technical
		/** @tags non-technical */
		return UText::localize("An empty text is not allowed.", self::class, $text_options);
	}
}
