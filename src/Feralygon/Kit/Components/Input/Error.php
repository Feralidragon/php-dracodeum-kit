<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input;

use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\Call as UCall;

/** @internal */
final class Error
{
	//Private properties
	/** @var mixed */
	private $value;
	
	/** @var \Closure[] */
	private $messengers = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param mixed $value
	 * <p>The value to instantiate with.</p>
	 * @param callable[] $messengers [default = []]
	 * <p>The messenger functions to use to get error messages from.<br>
	 * They are expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (\Feralygon\Kit\Options\Text $text_options): ?string</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>Feralygon\Kit\Options\Text $text_options</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The text options instance to use.<br>
	 * <br>
	 * Return: <code><b>string|null</b></code><br>
	 * The error message or <code>null</code> if no error is set.</p>
	 */
	final public function __construct($value, array $messengers = [])
	{
		//value
		$this->value = $value;
		
		//messengers
		if (!empty($messengers)) {
			$template = function (TextOptions $text_options): ?string {};
			foreach ($messengers as &$messenger) {
				UCall::assert('messenger', $messenger, $template);
				$messenger = \Closure::fromCallable($messenger);
				unset($messenger);
			}
			$this->messengers = $messengers;
		}
	}
	
	
	
	//Final public methods
	/**
	 * Get value.
	 * 
	 * @return mixed
	 * <p>The value.</p>
	 */
	final public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Get messenger functions.
	 * 
	 * @return \Closure[]
	 * <p>The messenger functions.</p>
	 */
	final public function getMessengers(): array
	{
		return $this->messengers;
	}
}
