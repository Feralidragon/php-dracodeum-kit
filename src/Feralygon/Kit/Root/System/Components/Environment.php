<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Components;

use Feralygon\Kit\Component;
use Feralygon\Kit\Root\System\Prototypes\{
	Environment as Prototype,
	Environments as Prototypes
};
use Feralygon\Kit\Prototype as ComponentPrototype;
use Feralygon\Kit\Root\System;
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This component represents an environment which sets the system configuration to use 
 * and how the code should run (debugging or production).
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System
 * @see \Feralygon\Kit\Root\System\Prototypes\Environment
 * @see \Feralygon\Kit\Root\System\Prototypes\Environments\Development
 * [prototype, name = 'development']
 * @see \Feralygon\Kit\Root\System\Prototypes\Environments\Staging
 * [prototype, name = 'staging']
 * @see \Feralygon\Kit\Root\System\Prototypes\Environments\Production
 * [prototype, name = 'production']
 */
class Environment extends Component
{
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getBasePrototypeClass() : string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Component\Traits\Prototypes)
	/** {@inheritdoc} */
	protected function buildPrototype(string $name, array $properties = []) : ?ComponentPrototype
	{
		switch ($name) {
			case 'development':
				return new Prototypes\Development($properties);
			case 'staging':
				return new Prototypes\Staging($properties);
			case 'production':
				return new Prototypes\Production($properties);
		}
		return null;
	}
	
	
	
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name defines an unique canonical identifier for this environment, 
	 * to be used to select which configuration profile to use.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName() : string
	{
		return $this->getPrototype()->getName();
	}
	
	/**
	 * Check if is a debug environment.
	 * 
	 * In a debug environment, the system behaves in such a way so that code can be easily debugged, 
	 * by performing additional integrity checks during runtime (assertions), 
	 * at the potential cost of lower performance and a higher memory footprint.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is a debug environment.</p>
	 */
	public function isDebug() : bool
	{
		return $this->getPrototype()->isDebug();
	}
	
	
	
	//Final public methods
	/**
	 * Apply.
	 * 
	 * This method may only be called from within the <code>setEnvironment</code> method 
	 * from the <code>Feralygon\Kit\Root\System</code> class.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final public function apply() : void
	{
		UCall::guard(System::isSettingEnvironment(), [
			'hint_message' => "This method may only be called from within the \"setEnvironment\" method " . 
				"from the \"Feralygon\\Kit\\Root\\System\" class."
		]);
		$this->getPrototype()->apply();
	}
}
