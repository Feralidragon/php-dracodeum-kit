<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Immutables;

use Feralygon\Kit\Immutable;

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
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('name')->setAsString(true)->setAsRequired();
		$this->addProperty('hostname')->setAsString(true)->setAsRequired();
		$this->addProperty('release')->setAsString(true)->setAsRequired();
		$this->addProperty('information')->setAsString(true)->setAsRequired();
		$this->addProperty('architecture')->setAsString(true)->setAsRequired();
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
