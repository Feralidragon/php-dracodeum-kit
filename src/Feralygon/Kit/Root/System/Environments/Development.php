<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Environments;

use Feralygon\Kit\Root\System\Environment;

/**
 * Root system development environment class.
 * 
 * @since 1.0.0
 */
class Development extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'development';
	}
	
	/** {@inheritdoc} */
	public function isDebug() : bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function canDisplayErrors() : bool
	{
		return true;
	}
	
	/** {@inheritdoc} */
	public function getErrorReportingFlags() : int
	{
		return E_ALL;
	}
}
