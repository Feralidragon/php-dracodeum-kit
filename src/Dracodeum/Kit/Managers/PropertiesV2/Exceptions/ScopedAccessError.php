<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Exception;
use Dracodeum\Kit\Attributes\Property\{
	Coercive,
	Mutator
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Utilities\Text as UText;

abstract class ScopedAccessError extends Exception
{
	//Public properties
	/** @var string[] */
	#[Coercive('string[]'), Mutator('non_empty')]
	public array $names;
	
	#[Coercive('?class')]
	public ?string $scope_class;
	
	
	
	//Abstract protected methods
	/**
	 * Get adjective.
	 * 
	 * @return string
	 * The adjective.
	 */
	abstract protected function getAdjective(): string;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		//initialize
		$adjective = $this->getAdjective();
		$substring = $this->scope_class !== null ? " from the {{scope_class}} class scope" : "";
		
		//return
		return Text::build()
			->setString("The property {{names}} from {{manager.getOwner()}} is not {$adjective}{$substring}.")
			->setPluralString("The properties {{names}} from {{manager.getOwner()}} are not {$adjective}{$substring}.")
			->setPluralNumber(count($this->names))
			->setPlaceholderStringifier('names', function (mixed $value, TextOptions $text_options): string {
				return UText::commify($value, $text_options, 'and', true);
			})
		;
	}
}
