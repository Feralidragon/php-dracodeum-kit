<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Hashes;

use Feralygon\Kit\Prototypes\Inputs\Hash;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Components\Input\Options\Info as InfoOptions;

/**
 * This input prototype represents a SHA-512 hash, as a string in hexadecimal notation.
 * 
 * Only the following types of values are able to be evaluated as a SHA-512 hash:<br>
 * &nbsp; &#8226; &nbsp; a hexadecimal notation string (128 bytes);<br>
 * &nbsp; &#8226; &nbsp; a colon-hexadecimal notation string, as octets or hextets (159 to 191 bytes);<br>
 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string (96 bytes);<br>
 * &nbsp; &#8226; &nbsp; a raw binary string (64 bytes).
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/SHA-2
 */
class Sha512 extends Hash
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getBits(): int
	{
		return 512;
	}
	
	
	
	//Overridden public methods
	/** {@inheritdoc} */
	public function getName(): string
	{
		return 'sha512';
	}
	
	/** {@inheritdoc} */
	public function getLabel(TextOptions $text_options, InfoOptions $info_options): string
	{
		return "SHA-512";
	}
}
