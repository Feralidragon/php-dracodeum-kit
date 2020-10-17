<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Structures;

use Dracodeum\Kit\Structure;

/**
 * Root system OS (Operating System) structure.
 * 
 * @property string $name
 * <p>The name.</p>
 * @property string $hostname
 * <p>The hostname.</p>
 * @property string $release
 * <p>The release.</p>
 * @property string $information
 * <p>The information.</p>
 * @property string $architecture
 * <p>The architecture.</p>
 */
final class Os extends Structure
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString(true);
		$this->addProperty('hostname')->setAsString(true);
		$this->addProperty('release')->setAsString(true);
		$this->addProperty('information')->setAsString(true);
		$this->addProperty('architecture')->setAsString(true);
	}
	
	
	
	//Final public methods
	/**
	 * Check if is Linux.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is Linux.</p>
	 */
	final public function isLinux(): bool
	{
		return $this->name === 'Linux';
	}
	
	/**
	 * Check if is Windows.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is Windows.</p>
	 */
	final public function isWindows(): bool
	{
		return strtoupper(substr($this->name, 0, 3)) === 'WIN';
	}
	
	/**
	 * Check if is Unix.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is Unix.</p>
	 */
	final public function isUnix(): bool
	{
		return !$this->isWindows();
	}
}
