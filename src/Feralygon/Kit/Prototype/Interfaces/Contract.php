<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototype\Interfaces;

/**
 * This interface defines a method to retrieve the contract from a prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototype
 */
interface Contract
{
	//Public static methods
	/**
	 * Get contract interface.
	 * 
	 * The returning contract interface must be implemented by the component using this prototype.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The contract interface.</p>
	 */
	public static function getContract() : string;
}