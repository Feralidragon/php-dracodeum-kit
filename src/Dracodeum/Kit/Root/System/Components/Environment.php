<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Components;

use Dracodeum\Kit\Component;
use Dracodeum\Kit\Root\System\Prototypes\{
	Environment as Prototype,
	Environments as Prototypes
};
use Dracodeum\Kit\Root\System;
use Dracodeum\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This component represents an environment which sets the system configuration to use 
 * and how the code should run (debugging or production).
 * 
 * @see \Dracodeum\Kit\Root\System\Prototypes\Environment
 * @see \Dracodeum\Kit\Root\System\Prototypes\Environments\Debug
 * [prototype, name = 'debug']
 * @see \Dracodeum\Kit\Root\System\Prototypes\Environments\Development
 * [prototype, name = 'development']
 * @see \Dracodeum\Kit\Root\System\Prototypes\Environments\Staging
 * [prototype, name = 'staging']
 * @see \Dracodeum\Kit\Root\System\Prototypes\Environments\Production
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
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Component\Traits\PrototypeProducer)
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
	 * @see \Dracodeum\Kit\Root\System\Enumerations\DumpVerbosityLevel
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
	 * from the <code>Dracodeum\Kit\Root\System</code> class.
	 * 
	 * @return void
	 */
	final public function apply(): void
	{
		UCall::guard(System::isSettingEnvironment(), [
			'hint_message' => "This method may only be called from within the \"setEnvironment\" method " . 
				"from the \"Dracodeum\\Kit\\Root\\System\" class."
		]);
		$this->getPrototype()->apply();
	}
}
