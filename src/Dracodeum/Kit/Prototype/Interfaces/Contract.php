<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototype\Interfaces;

/** This interface defines a method to get the contract from a prototype. */
interface Contract
{
	//Public static methods
	/**
	 * Get contract interface.
	 * 
	 * The returning contract interface must be implemented by the component using this prototype.
	 * 
	 * @return string
	 * <p>The contract interface.</p>
	 */
	public static function getContract(): string;
}
