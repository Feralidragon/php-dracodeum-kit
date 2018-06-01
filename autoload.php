<?php

/**
 * Require or include this file in order to use this package.
 * 
 * @version 1.0.0
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

use Feralygon\Kit\Root\Loader;



//avoid the loader to be loaded again
if (class_exists(Loader::class, false)) {
	return false;
}

//constants
define('FERALYGON_KIT_VERSION', '1.0.0');
define('FERALYGON_KIT_DIRECTORY', __DIR__ . '/src');

//required classes
require_once FERALYGON_KIT_DIRECTORY . '/Feralygon/Kit/Root/Loader.php';
require_once FERALYGON_KIT_DIRECTORY . '/Feralygon/Kit/Root/Loader/Package.php';

//set package
Loader::setPackage('feralygon', 'kit', FERALYGON_KIT_DIRECTORY);

//return
return true;
