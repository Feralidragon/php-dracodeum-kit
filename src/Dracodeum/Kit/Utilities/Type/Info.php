<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type;

use Dracodeum\Kit\Utilities\Type\Info\Enumerations\Kind as EKind;

final class Info
{
	//Public properties
	/** @var enum<\Dracodeum\Kit\Utilities\Type\Info\Enumerations\Kind> */
	public $kind; //TODO: set as readonly
	
	public ?string $name; //TODO: set as readonly
	
	/** @var string[] */
	public array $names; //TODO: set as readonly
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Utilities\Type\Info\Enumerations\Kind>> $kind
	 * The kind to instantiate with.
	 * 
	 * @param string|null $name
	 * The name to instantiate with.
	 * 
	 * @param string[] $names
	 * The names to instantiate with.
	 */
	final public function __construct($kind, ?string $name = null, array $names = [])
	{
		$this->kind = EKind::coerceValue($kind);
		$this->name = $name;
		$this->names = $names;
	}
}
