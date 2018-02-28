<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory;

use Feralygon\Kit\Traits;
use Feralygon\Kit\Factory\Builder\Exceptions;

/**
 * Factory builder class.
 * 
 * This class is the base to be extended from when creating a builder.<br>
 * <br>
 * A builder is responsible for building objects for a factory, 
 * and it must implement at least one of the following interfaces:<br>
 * &nbsp; &#8226; &nbsp; <code>Feralygon\Kit\Factory\Builder\Interfaces\Build</code> : to build an object;<br>
 * &nbsp; &#8226; &nbsp; <code>Feralygon\Kit\Factory\Builder\Interfaces\NamedBuild</code> : 
 * to build an object by using a given name.<br>
 * <br>
 * For more information, please check the <code>Feralygon\Kit\Factory</code> class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Factory
 * @see \Feralygon\Kit\Factory\Builder\Interfaces\Build
 * @see \Feralygon\Kit\Factory\Builder\Interfaces\NamedBuild
 */
abstract class Builder
{
	//Traits
	use Traits\NoConstructor;
	
	
	
	//Final protected methods
	/**
	 * Validate if the number of a given set of arguments is within a given range.
	 * 
	 * @since 1.0.0
	 * @param array $arguments <p>The arguments to validate.</p>
	 * @param int $minimum <p>The minimum number of arguments to validate against.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $maximum [default = null] <p>The maximum number of arguments to validate against.<br>
	 * If set, it must be greater than or equal to <code>0</code>.<br>
	 * If not set, any number of arguments above the given minimum are allowed.</p>
	 * @throws \Feralygon\Kit\Factory\Builder\Exceptions\InvalidArgumentsMinimum
	 * @throws \Feralygon\Kit\Factory\Builder\Exceptions\InvalidArgumentsMaximum
	 * @throws \Feralygon\Kit\Factory\Builder\Exceptions\TooFewArguments
	 * @throws \Feralygon\Kit\Factory\Builder\Exceptions\TooManyArguments
	 * @return void
	 */
	final protected function validateArgumentsRange(array $arguments, int $minimum, ?int $maximum = null) : void
	{
		$count = count($arguments);
		if ($minimum < 0) {
			throw new Exceptions\InvalidArgumentsMinimum(['builder' => $this, 'minimum' => $minimum]);
		} elseif (isset($maximum) && $maximum < 0) {
			throw new Exceptions\InvalidArgumentsMaximum(['builder' => $this, 'maximum' => $maximum]);
		} elseif ($count < $minimum) {
			throw new Exceptions\TooFewArguments(['builder' => $this, 'count' => $count, 'minimum' => $minimum]);
		} elseif (isset($maximum) && $count > $maximum) {
			throw new Exceptions\TooManyArguments(['builder' => $this, 'count' => $count, 'maximum' => $maximum]);
		}
	}
	
	/**
	 * Validate if the number of a given set of arguments corresponds exactly to a given count.
	 * 
	 * @since 1.0.0
	 * @param array $arguments <p>The arguments to validate.</p>
	 * @param int $count <p>The exact number of arguments to validate against.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return void
	 */
	final protected function validateArgumentsCount(array $arguments, int $count) : void
	{
		$this->validateArgumentsRange($arguments, $count, $count);
	}
	
	/**
	 * Validate if the number of a given set of arguments is greater than or equal to a given minimum.
	 * 
	 * @since 1.0.0
	 * @param array $arguments <p>The arguments to validate.</p>
	 * @param int $minimum <p>The minimum number of arguments to validate against.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return void
	 */
	final protected function validateArgumentsMinimum(array $arguments, int $minimum) : void
	{
		$this->validateArgumentsRange($arguments, $minimum);
	}
	
	/**
	 * Validate if the number of a given set of arguments is less than or equal to a given maximum.
	 * 
	 * @since 1.0.0
	 * @param array $arguments <p>The arguments to validate.</p>
	 * @param int $maximum <p>The maximum number of arguments to validate against.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @return void
	 */
	final protected function validateArgumentsMaximum(array $arguments, int $maximum) : void
	{
		$this->validateArgumentsRange($arguments, 0, $maximum);
	}
	
	/**
	 * Map a given set of arguments to a given set of keys.
	 * 
	 * The mapping process consists in converting the given set of arguments so that each one can be referenced through 
	 * each given corresponding key, resulting in a set of arguments as <samp>key => argument</samp> pairs.<br>
	 * <br>
	 * The given number of arguments must be greater than or equal to the given number of keys.
	 * 
	 * @since 1.0.0
	 * @param array $arguments [reference] <p>The arguments to map.</p>
	 * @param array $keys <p>The keys to map with, as any possible combination of the following:<br>
	 * &nbsp; &#8226; &nbsp; an array of keys;<br>
	 * &nbsp; &#8226; &nbsp; an array as <code>key => value</code> pairs, in which each value represents 
	 * the default value to use for each key, if the corresponding argument is not given.
	 * </p>
	 * @param array $remaining [reference output] [default = []] 
	 * <p>The remaining arguments which were not mapped, due to missing corresponding keys.</p>
	 * @throws \Feralygon\Kit\Factory\Builder\Exceptions\MissingArgumentsForKeys
	 * @return void
	 */
	final protected function mapArguments(array &$arguments, array $keys, array &$remaining = []) : void
	{
		//initialize
		$remaining = $mandatory = $defaults = [];
		foreach ($keys as $k => $key) {
			if (is_int($k)) {
				if (!empty($defaults)) {
					$mandatory = array_merge($mandatory, array_keys($defaults));
					$defaults = [];
				}
				$mandatory[] = $key;
			} else {
				$defaults[$k] = $key;
			}
		}
		
		//validate
		$arguments_count = count($arguments);
		$mandatory_count = count($mandatory);
		if ($arguments_count < $mandatory_count) {
			throw new Exceptions\MissingArgumentsForKeys([
				'builder' => $this, 'keys' => array_slice($mandatory, $arguments_count)
			]);
		}
		
		//map
		$args = array_combine($mandatory, array_slice($arguments, 0, $mandatory_count));
		if (!empty($defaults)) {
			$default_args = array_slice($arguments, $mandatory_count, count($defaults));
			$default_keys = array_keys(array_slice($defaults, 0, count($default_args), true));
			$args += array_combine($default_keys, $default_args) + $defaults;
		}
		
		//finish
		$remaining = array_slice($arguments, count($keys));
		$arguments = $args;
	}
}
