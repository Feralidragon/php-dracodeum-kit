<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type;

/** @internal */
final class Protoname
{
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Protoname\Enumerations\Type> $type
	 * The type to instantiate with.
	 * 
	 * @param string[] $names
	 * The names to instantiate with.
	 */
	final public function __construct(
		public string $type,
		public array $names
	) {}
}
