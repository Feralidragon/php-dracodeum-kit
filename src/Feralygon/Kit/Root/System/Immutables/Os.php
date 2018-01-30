<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Immutables;

use Feralygon\Kit\Core\Immutable;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Root system OS (Operating System) immutable class.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The name.</p>
 * @property-read string $hostname <p>The hostname.</p>
 * @property-read string $release <p>The release.</p>
 * @property-read string $information <p>The information.</p>
 * @property-read string $architecture <p>The architecture.</p>
 * @see \Feralygon\Kit\Root\System
 */
final class Os extends Immutable
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['name', 'hostname', 'release', 'information', 'architecture'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				//no break
			case 'hostname':
				//no break
			case 'release':
				//no break
			case 'information':
				//no break
			case 'architecture':
				return UType::evaluateString($value, true);
		}
		return null;
	}
	
	
	
	//Final public methods
	/**
	 * Check if is Linux.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is Linux.</p>
	 */
	final public function isLinux() : bool
	{
		return $this->get('name') === 'Linux';
	}
	
	/**
	 * Check if is Windows.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is Windows.</p>
	 */
	final public function isWindows() : bool
	{
		return strtoupper(substr($this->get('name'), 0, 3)) === 'WIN';
	}
	
	/**
	 * Check if is Unix.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is Unix.</p>
	 */
	final public function isUnix() : bool
	{
		return !$this->isWindows();
	}
}
