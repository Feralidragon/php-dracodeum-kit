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
use Feralygon\Kit\Root\System;
use Feralygon\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This component represents an environment which sets the system configuration to use 
 * and how the code should run (debugging or production).
 * 
 * @see \Feralygon\Kit\Root\System\Prototypes\Environment
 * @see \Feralygon\Kit\Root\System\Prototypes\Environments\Debug
 * [prototype, name = 'debug']
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
	public static function getBasePrototypeClass(): string
	{
		return Prototype::class;
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Component\Traits\PrototypeProducer)
	/** {@inheritdoc} */
	protected function producePrototype(string $name, array $properties)
	{
		switch ($name) {
			case 'debug':
				return Prototypes\Debug::class;
			case 'development':
				return Prototypes\Development::class;
			case 'staging':
				return Prototypes\Staging::class;
			case 'production':
				return Prototypes\Production::class;
		}
		return null;
	}
	
	
	
	//Public methods
	/**
	 * Get name.
	 * 
	 * The returning name defines a unique canonical identifier for this environment, 
	 * to be used to select which configuration profile to use.
	 * 
	 * @return string
	 * <p>The name.</p>
	 */
	public function getName(): string
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
	 * @return bool
	 * <p>Boolean <code>true</code> if is a debug environment.</p>
	 */
	public function isDebug(): bool
	{
		return $this->getPrototype()->isDebug();
	}
	
	/**
	 * Get dump verbosity level.
	 * 
	 * @see \Feralygon\Kit\Root\System\Enumerations\DumpVerbosityLevel
	 * @return int
	 * <p>The dump verbosity level.</p>
	 */
	public function getDumpVerbosityLevel(): int
	{
		return EDumpVerbosityLevel::coerceValue($this->getPrototype()->getDumpVerbosityLevel());
	}
	
	
	
	//Final public methods
	/**
	 * Apply.
	 * 
	 * This method may only be called from within the <code>setEnvironment</code> method 
	 * from the <code>Feralygon\Kit\Root\System</code> class.
	 * 
	 * @return void
	 */
	final public function apply(): void
	{
		UCall::guard(System::isSettingEnvironment(), [
			'hint_message' => "This method may only be called from within the \"setEnvironment\" method " . 
				"from the \"Feralygon\\Kit\\Root\\System\" class."
		]);
		$this->getPrototype()->apply();
	}
}
