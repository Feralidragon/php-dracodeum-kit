<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type;

use Dracodeum\Kit\Utilities\Type\Info\Enums\Kind as EKind;
use Dracodeum\Kit\Exceptions\Argument\Invalid as InvalidArgument;

final readonly class Info
{
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param \Dracodeum\Kit\Utilities\Type\Info\Enums\Kind $kind
	 * The kind to instantiate with.
	 * 
	 * @param string|null $name
	 * The name to instantiate with.
	 * 
	 * @param string[] $names
	 * The names to instantiate with.
	 * 
	 * @param string $flags
	 * The flags to instantiate with.
	 * 
	 * @param array $parameters
	 * The parameters to instantiate with.
	 * 
	 * @throws \Dracodeum\Kit\Exceptions\Argument\Invalid
	 */
	final public function __construct(
		public EKind $kind,
		public ?string $name = null,
		public array $names = [],
		public string $flags = '',
		public array $parameters = []
	) {
		if (!array_is_list($names)) {
			throw new InvalidArgument(
				'names', $names, error_message: "Only a non-associative string array is expected to be given."
			);
		}
	}
}
