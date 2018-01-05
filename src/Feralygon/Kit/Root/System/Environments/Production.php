<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Environments;

use Feralygon\Kit\Root\System\Environment;

/**
 * Root system production environment class.
 * 
 * @since 1.0.0
 */
class Production extends Environment
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getName() : string
	{
		return 'production';
	}
	
	/** {@inheritdoc} */
	public function isDebug() : bool
	{
		return false;
	}
	
	/** {@inheritdoc} */
	public function canDisplayErrors() : bool
	{
		return false;
	}
	
	/** {@inheritdoc} */
	public function getErrorReportingFlags() : int
	{
		return E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED;
	}
}
