<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Builder\Exceptions;

use Feralygon\Kit\Factory\Builder\Exception;
use Feralygon\Kit\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Factory builder missing arguments for keys exception class.
 * 
 * This exception is thrown from a builder whenever arguments are missing for a given set of keys.
 * 
 * @since 1.0.0
 * @property-read string[] $keys <p>The keys.</p>
 */
class MissingArgumentsForKeys extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return count($this->get('keys')) === 1
			? "Missing argument for key {{keys}} in builder {{builder}}."
			: "Missing arguments for keys {{keys}} in builder {{builder}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('keys')
			->setAsArray(function (&$key, &$value) : bool {
				return UType::evaluateString($value);
			}, true, true)
			->setAsRequired()
		;
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'keys') {
			return UText::stringify($value, null, [
				'quote_strings' => true,
				'non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND
			]);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
