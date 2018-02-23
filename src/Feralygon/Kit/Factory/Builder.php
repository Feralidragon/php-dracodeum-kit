<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory;

use Feralygon\Kit\Traits;

/**
 * Factory builder class.
 * 
 * This class is the base to be extended from when creating a factory builder.<br>
 * For more information, please check the <code>Feralygon\Kit\Factory</code> class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory
 */
abstract class Builder
{
	//Traits
	use Traits\NoConstructor;
	
	
	
	//Abstract public methods
	/**
	 * Build object for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The name to build for.</p>
	 * @param mixed ...$arguments <p>The arguments to build with.</p>
	 * @return object|null <p>The built object for the given name or <code>null</code> if none was built.</p>
	 */
	abstract public function build(string $name, ...$arguments) : ?object;
	
	
	
	//Final protected methods
	/**
	 * Validate the number of a given set of arguments.
	 * 
	 * @since 1.0.0
	 * @param array $arguments <p>The arguments to validate.</p>
	 * @param int $minimum <p>The minimum number of allowed arguments validate with.<br>
	 * It must be equal to or greater than <code>0</code>.</p>
	 * @param int|null $maximum [default = null] <p>The maximum number of allowed arguments validate with.<br>
	 * If set, it must be equal to or greater than <code>0</code>.<br>
	 * If not set, any number of arguments above the minimum are allowed.</p>
	 * @return void
	 */
	final protected function validateArguments(array $arguments, int $minimum, ?int $maximum = null) : void
	{
		$count = count($arguments);
		if ($minimum < 0) {
			
			//TODO: throw exception
			
		} elseif ($count < $minimum) {
			
			//TODO: throw exception
			
		} elseif (isset($maximum)) {
			if ($maximum < 0) {
				
				//TODO: throw exception
				
			} elseif ($count > $maximum) {
				
				//TODO: throw exception
				
			}
		}
	}
	
	/**
	 * Map a given set of arguments to a given set of keys.
	 * 
	 * The mapping process consists in converting the given set of arguments so that each one can be referenced through 
	 * each given corresponding key, resulting in a set of arguments as <samp>key => argument</samp> pairs.<br>
	 * <br>
	 * The given number of arguments must be equal to or greater than the given number of keys.
	 * 
	 * @since 1.0.0
	 * @param array $arguments [reference] <p>The arguments to map.</p>
	 * @param string[] $keys <p>The keys to map with.</p>
	 * @param array $remaining [reference output] [default = []] 
	 * <p>The remaining arguments which failed to be mapped, due to missing corresponding keys.</p>
	 * @return void
	 */
	final protected function mapArguments(array &$arguments, array $keys, array &$remaining = []) : void
	{
		$remaining = [];
		$args_count = count($arguments);
		$keys_count = count($keys);
		if ($args_count < $keys_count) {
			
			//TODO: throw exception
			
		}
		$remaining = array_slice($arguments, $keys_count);
		$arguments = array_combine($keys, array_slice($arguments, 0, $keys_count));
	}
}
