<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Inputs\Hashes;

use Feralygon\Kit\Core\Prototypes\Inputs\Hash;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Components\Input\Options\Info as InfoOptions;

/**
 * Core SHA-256 hash input prototype class.
 * 
 * This input prototype represents a SHA-256 hash, as a string in hexadecimal notation, in which only the following types of values are able to be evaluated as such:<br>
 * &nbsp; &#8226; &nbsp; a hexadecimal notation string (64 bytes);<br>
 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string (48 bytes);<br>
 * &nbsp; &#8226; &nbsp; a raw binary string (32 bytes).
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/SHA-2
 */
class Sha256 extends Hash
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getBits() : int
	{
		return 256;
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'sha256';
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options) : string
	{
		return "SHA-256";
	}
}
