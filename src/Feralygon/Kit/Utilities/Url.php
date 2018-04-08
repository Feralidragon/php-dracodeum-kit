<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities;

use Feralygon\Kit\Utility;
use Feralygon\Kit\Utilities\Url\{
	Options,
	Exceptions
};
use Feralygon\Kit\Interfaces\{
	Arrayable as IArrayable,
	Stringifiable as IStringifiable
};

/**
 * This utility implements a set of methods used to manipulate and retrieve URL information.
 * 
 * @since 1.0.0
 */
final class Url extends Utility
{
	//Final public static methods
	/**
	 * Querify a given set of parameters.
	 * 
	 * The process of querification of a given set of parameters consists in converting all the given parameters 
	 * into a query string.
	 * 
	 * @since 1.0.0
	 * @param array $parameters
	 * <p>The parameters to querify, as <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Utilities\Url\Options\Querify|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Utilities\Url\Exceptions\Querify\UnsupportedParameterType
	 * @return string|null
	 * <p>The querified parameters into a query string or <code>null</code> if an empty set of parameters was given.</p>
	 */
	final public static function querify(array $parameters, $options = null) : ?string
	{
		//initialize
		if (empty($parameters)) {
			return null;
		}
		$options = Options\Querify::coerce($options);
		$allow_arrays = $options->allow_arrays;
		$no_encode = $options->no_encode;
		
		//querify
		$f = function (array $values, ?string $name = null) use (&$f, $allow_arrays, $no_encode) : array {
			$query = [];
			$associative = Data::isAssociative($values);
			foreach ($values as $key => $value) {
				//array
				if ($allow_arrays && (is_array($value) || (is_object($value) && $value instanceof IArrayable))) {
					$query = array_merge($query, $f(
						is_object($value) ? $value->toArray() : $value, isset($name) ? "{$name}[{$key}]" : $key
					));
					continue;
				}
				
				//key
				$k = isset($name) ? ($associative ? "{$name}[{$key}]" : "{$name}[]") : $key;
				
				//value
				$v = null;
				if (!isset($value)) {
					continue;
				} elseif (is_bool($value)) {
					$v = $value ? '1' : '0';
				} elseif (is_int($value) || is_float($value)) {
					$v = (string)$value;
				} elseif (is_string($value)) {
					$v = $value;
				} elseif (is_object($value) && $value instanceof IStringifiable) {
					$v = $value->toString();
				} elseif (is_object($value) && method_exists($value, '__toString')) {
					$v = (string)$value;
				} else {
					throw new Exceptions\Querify\UnsupportedParameterType(['name' => $k, 'value' => $value]);
				}
				
				//query
				$query[] = $no_encode ? "{$k}={$v}" : urlencode($k) . '=' . urlencode($v);
			}
			return $query;
		};
		return implode($options->delimiter, $f($parameters));
	}
	
	/**
	 * Unquerify a given string.
	 * 
	 * The process of unquerification of a given string consists in parsing it into a set of key-value parameters.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to unquerify.</p>
	 * @param \Feralygon\Kit\Utilities\Url\Options\Unquerify|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return array
	 * <p>The unquerified array from the given string, as <samp>name => value</samp> pairs.</p>
	 */
	final public static function unquerify(string $string, $options = null) : array
	{
		//initialize
		$options = Options\Unquerify::coerce($options);
		$allow_arrays = $options->allow_arrays;
		
		//unquerify
		$parameters = [];
		foreach (preg_split('/[;&]/', $string) as $pair) {
			//parse
			if (!preg_match('/^(?P<name>[^=]+)=(?P<value>[^=]*)$/', $pair, $matches)) {
				continue;
			}
			$name = urldecode($matches['name']);
			$value = urldecode($matches['value']);
			unset($matches);
			
			//process
			if ($allow_arrays && preg_match('/^(?P<name>[^\[\]]+)(?P<indexes>(?:\[[^\[\]]*\])+)$/', $name, $matches)) {
				//keys
				$name = $matches['name'];
				$indexes = $matches['indexes'];
				unset($matches);
				preg_match_all('/\[(?P<keys>[^\[\]]*)\]/', $indexes, $matches);
				$keys = $matches['keys'];
				unset($indexes, $matches);
				
				//parameters
				$pointer = &$parameters;
				array_unshift($keys, $name);
				foreach ($keys as $key) {
					if (!isset($pointer) || !is_array($pointer)) {
						$pointer = [];
					}
					$pointer = &$pointer[$key === '' ? count($pointer) : $key];
				}
				$pointer = $value;
				unset($pointer);
			} else {
				$parameters[$name] = $value;
			}
		}
		return $parameters;
	}
}
