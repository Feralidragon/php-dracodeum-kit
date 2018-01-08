<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\{
	Traits,
	Utility
};
use Feralygon\Kit\Core\Utilities\Call\Exceptions;
use Feralygon\Kit\Root\System;

/**
 * Core call utility class.
 * 
 * This utility implements a set of methods used to retrieve information from existing PHP functions, methods or callables.
 * 
 * @since 1.0.0
 * @see https://php.net/manual/en/language.functions.php
 * @see https://php.net/manual/en/language.types.callable.php
 */
final class Call extends Utility
{
	//Traits
	use Traits\Memoization;
	
	
	
	//Public constants
	/** Parameters constants values (flag). */
	public const PARAMETERS_CONSTANTS_VALUES = 0x01;
	
	/** Parameters classes short names (flag). */
	public const PARAMETERS_CLASSES_SHORT_NAMES = 0x02;
	
	/** Parameters classes leading slash (flag). */
	public const PARAMETERS_CLASSES_LEADING_SLASH = 0x04;
	
	/** Parameters no mixed type (flag). */
	public const PARAMETERS_NO_MIXED_TYPE = 0x08;
	
	/** Type no mixed (flag). */
	public const TYPE_NO_MIXED = 0x01;
	
	/** Type class short name (flag). */
	public const TYPE_CLASS_SHORT_NAME = 0x02;
	
	/** Type class leading slash (flag). */
	public const TYPE_CLASS_LEADING_SLASH = 0x04;
	
	/** Header constants values (flag). */
	public const HEADER_CONSTANTS_VALUES = 0x01;
	
	/** Header classes short names (flag). */
	public const HEADER_CLASSES_SHORT_NAMES = 0x02;
	
	/** Header classes leading slash (flag). */
	public const HEADER_CLASSES_LEADING_SLASH = 0x04;
	
	/** Header no mixed type (flag). */
	public const HEADER_NO_MIXED_TYPE = 0x08;
	
	/** Source constants values (flag). */
	public const SOURCE_CONSTANTS_VALUES = 0x01;
	
	/** Source classes short names (flag). */
	public const SOURCE_CLASSES_SHORT_NAMES = 0x02;
	
	/** Source classes leading slash (flag). */
	public const SOURCE_CLASSES_LEADING_SLASH = 0x04;
	
	/** Source no mixed type (flag). */
	public const SOURCE_NO_MIXED_TYPE = 0x08;
	
	
	
	//Final public static methods
	/**
	 * Retrieve a new reflection instance for a given function.
	 * 
	 * The returning reflection instance depends on the type of function given.<br>
	 * In the case of a class or instance method, a reflection instance of the <code>ReflectionMethod</code> class is returned.<br>
	 * If, however, it's any other type of function, such as a global, local or anonymous function, 
	 * a reflection instance of the <code>ReflectionFunction</code> class is returned instead.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/class.reflectionfunction.php
	 * @see https://php.net/manual/en/class.reflectionmethod.php
	 * @param callable $function <p>The function to retrieve for.</p>
	 * @return \ReflectionFunction|\ReflectionMethod <p>A new reflection instance for the given function.</p>
	 */
	final public static function reflection(callable $function) : \ReflectionFunctionAbstract
	{
		//array form
		if (is_array($function)) {
			$function = (is_object($function[0]) ? get_class($function[0]) : $function[0]) . '::' . $function[1];
		}
		
		//method
		if (is_string($function)) {
			$function = str_replace('->', '::', $function);
			if (strpos($function, '::') !== false) {
				return new \ReflectionMethod($function);
			}
		}
		
		//function
		return new \ReflectionFunction($function);
	}
	
	/**
	 * Calculate hash from a given function.
	 * 
	 * The returning hash of the given function is calculated mostly based on its declaration location, 
	 * but also its signature, therefore it's unique to the function signature and scope, thus resulting in different 
	 * hashes even if two functions share exactly the same signature and source provided that they are declared in different locations.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.hash.php
	 * @param callable $function <p>The function to calculate from.</p>
	 * @param string $algorithm [default = 'SHA1'] <p>The hash algorithm to use, which can be any supported by the PHP core <code>hash</code> function.</p>
	 * @param bool $raw [default = false] <p>Return the raw binary form of the hash, instead of its human-readable hexadecimal representation.</p>
	 * @return string <p>The hash from the given function.</p>
	 */
	final public static function hash(callable $function, string $algorithm = 'SHA1', bool $raw = false) : string
	{
		return self::memoize(function () use ($function, $algorithm, $raw) : string {
			$reflection = self::reflection($function);
			$export = get_class($reflection) === \ReflectionMethod::class
				? $reflection::export($reflection->getDeclaringClass()->getName(), $reflection->getName(), true)
				: $reflection::export($reflection->getClosure(), true);
			return hash($algorithm, $export, $raw);
		});
	}
	
	/**
	 * Retrieve modifiers from a given function.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @return string[] <p>The modifiers from the given function.</p>
	 */
	final public static function modifiers(callable $function) : array
	{
		//reflection
		$reflection = self::reflection($function);
		if (get_class($reflection) !== \ReflectionMethod::class) {
			return [];
		}
		
		//modifiers
		$modifiers = [];
		$flags = $reflection->getModifiers();
		if ($flags & \ReflectionMethod::IS_ABSTRACT) {
			$modifiers[] = 'abstract';
		} elseif ($flags & \ReflectionMethod::IS_FINAL) {
			$modifiers[] = 'final';
		}
		if ($flags & \ReflectionMethod::IS_PUBLIC) {
			$modifiers[] = 'public';
		} elseif ($flags & \ReflectionMethod::IS_PROTECTED) {
			$modifiers[] = 'protected';
		} elseif ($flags & \ReflectionMethod::IS_PRIVATE) {
			$modifiers[] = 'private';
		}
		if ($flags & \ReflectionMethod::IS_STATIC) {
			$modifiers[] = 'static';
		}
		return $modifiers;
	}
	
	/**
	 * Retrieve name from a given function.
	 * 
	 * If the given function is anonymous then <samp>null</samp> is returned.<br>
	 * If the given function belongs to a class and the <var>$full</var> parameter is passed as <code>true</code>, 
	 * then a string in the format <samp>Class::name</samp> is returned.<br>
	 * In every other case only the name itself is returned.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @param bool $full [default = false] <p>Return the full name, including the class it's declared in.</p>
	 * @param bool $short [default = false] <p>Return the short form of the class name instead of the full namespaced one.</p>
	 * @return string|null <p>The name from the given function or <samp>null</samp> if the function has no name (anonymous).</p>
	 */
	final public static function name(callable $function, bool $full = false, bool $short = false) : ?string
	{
		//optimization
		if (!$full) {
			if (is_array($function)) {
				return $function[1];
			} elseif (is_string($function)) {
				$parts = explode('::', $function);
				return end($parts);
			}
		}
		
		//reflection
		$reflection = self::reflection($function);
		$name = $reflection->getName();
		if ($name === '' || $name === '{closure}') {
			return null;
		} elseif ($full && get_class($reflection) === \ReflectionMethod::class) {
			$reflection_class = $reflection->getDeclaringClass();
			$name = ($short ? $reflection_class->getShortName() : $reflection_class->getName()) . "::{$name}";
		}
		return $name;
	}
	
	/**
	 * Retrieve parameters from a given function.
	 * 
	 * The returning parameters from the given function are represented by their types, names and default values.<br>
	 * The expected return format for each parameter is <samp>type name</samp> or <samp>type name = value</samp>.<br>
	 * <br>
	 * In parameters passed by reference, an additional <samp>&amp;</samp> is prepended.<br>
	 * In variadic parameters, an additional <samp>...</samp> is also prepended.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @param int $flags [default = 0x00] <p>The parameters bitwise flags, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CONSTANTS_VALUES</code> : Return the constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CLASSES_SHORT_NAMES</code> : Return short names for classes instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_CLASSES_LEADING_SLASH</code> : Return classes with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::PARAMETERS_NO_MIXED_TYPE</code> : Do not return mixed parameters with the <code>mixed</code> type keyword.
	 * </p>
	 * @return string[] <p>The parameters from the given function.</p>
	 */
	final public static function parameters(callable $function, int $flags = 0x00) : array
	{
		return self::memoize(function () use ($function, $flags) : array {
			//initialize
			$reflection = self::reflection($function);
			$is_method = get_class($reflection) === \ReflectionMethod::class;
			
			//parameters
			$parameters = [];
			foreach ($reflection->getParameters() as $parameter) {
				//type
				$type = ($flags & self::PARAMETERS_NO_MIXED_TYPE) ? '' : 'mixed';
				$ptype = $parameter->getType();
				if (isset($ptype)) {
					$type = (string)$ptype;
					if ($flags & (self::PARAMETERS_CLASSES_SHORT_NAMES | self::PARAMETERS_CLASSES_LEADING_SLASH)) {
						$ptype_class = $parameter->getClass();
						if (isset($ptype_class)) {
							if ($flags & self::PARAMETERS_CLASSES_SHORT_NAMES) {
								$type = $ptype_class->getShortName();
							} elseif ($flags & self::PARAMETERS_CLASSES_LEADING_SLASH) {
								$type = "\\{$type}";
							}
						}
					}
					if ($ptype->allowsNull()) {
						$type = "?{$type}";
					}
				}
				
				//name
				$name = '$' . $parameter->getName();
				if ($parameter->isPassedByReference()) {
					$name = "&{$name}";
				}
				if ($parameter->isVariadic()) {
					$name = "...{$name}";
				}
				
				//value
				$value = '';
				if ($parameter->isDefaultValueAvailable()) {
					$value = ' = ';
					if (!($flags & self::PARAMETERS_CONSTANTS_VALUES) && $parameter->isDefaultValueConstant()) {
						$constant = $parameter->getDefaultValueConstantName();
						if (strpos($constant, '::') !== false) {
							if ($is_method) {
								$constant = str_replace('self::', "{$parameter->getDeclaringClass()->getName()}::", $constant);
							}
							if ($flags & self::PARAMETERS_CLASSES_SHORT_NAMES) {
								[$constant_class, $constant_name] = explode('::', $constant);
								$constant = Type::basename($constant_class) . '::' . $constant_name;
							} elseif ($flags & self::PARAMETERS_CLASSES_LEADING_SLASH) {
								$constant = "\\{$constant}";
							}
						}
						$value .= $constant;
					} else {
						$value .= Type::phpfy($parameter->getDefaultValue());
					}
				}
				
				//append
				$parameters[] = trim("{$type} {$name}{$value}");
			}
			
			//return
			return $parameters;
		});
	}
	
	/**
	 * Retrieve type from a given function.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @param int $flags [default = 0x00] <p>The type bitwise flags, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_NO_MIXED</code> : Do not return the <code>mixed</code> type keyword.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_CLASS_SHORT_NAME</code> : Return a short name for a class instead of a full namespaced name.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TYPE_CLASS_LEADING_SLASH</code> : Return class with the leading slash.
	 * </p>
	 * @return string <p>The type from the given function.</p>
	 */
	final public static function type(callable $function, int $flags = 0x00) : string
	{
		$type = ($flags & self::TYPE_NO_MIXED) ? '' : 'mixed';
		$rtype = self::reflection($function)->getReturnType();
		if (isset($rtype)) {
			$type = (string)$rtype;
			if (($flags & (self::TYPE_CLASS_SHORT_NAME | self::TYPE_CLASS_LEADING_SLASH)) && class_exists($type)) {
				if ($flags & self::TYPE_CLASS_SHORT_NAME) {
					$type = Type::basename($type);
				} elseif ($flags & self::TYPE_CLASS_LEADING_SLASH) {
					$type = "\\{$type}";
				}
			}
			if ($rtype->allowsNull()) {
				$type = "?{$type}";
			}
		}
		return $type;
	}
	
	/**
	 * Retrieve header from a given function.
	 * 
	 * The returning header from the given function is represented by its modifiers, name, parameters and type.<br>
	 * The expected return format is <samp>modifier function name(type1 param1, type2 param2 = value2, ...) : type</samp> .
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @param int $flags [default = 0x00] <p>The header bitwise flags, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CONSTANTS_VALUES</code> : Return the constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CLASSES_SHORT_NAMES</code> : Return short names for classes instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_CLASSES_LEADING_SLASH</code> : Return classes with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::HEADER_NO_MIXED_TYPE</code> : Do not return a mixed type nor parameters with the <code>mixed</code> type keyword.
	 * </p>
	 * @return string <p>The header from the given function.</p>
	 */
	final public static function header(callable $function, int $flags = 0x00) : string
	{
		//initialize
		$modifiers = self::modifiers($function);
		$name = self::name($function);
		
		//initialize parameters
		$parameters_flags = 0x00;
		if ($flags & self::HEADER_CONSTANTS_VALUES) {
			$parameters_flags |= self::PARAMETERS_CONSTANTS_VALUES;
		}
		if ($flags & self::HEADER_CLASSES_SHORT_NAMES) {
			$parameters_flags |= self::PARAMETERS_CLASSES_SHORT_NAMES;
		}
		if ($flags & self::HEADER_CLASSES_LEADING_SLASH) {
			$parameters_flags |= self::PARAMETERS_CLASSES_LEADING_SLASH;
		}
		if ($flags & self::HEADER_NO_MIXED_TYPE) {
			$parameters_flags |= self::PARAMETERS_NO_MIXED_TYPE;
		}
		$parameters = self::parameters($function, $parameters_flags);
		
		//initialize type
		$type_flags = 0x00;
		if ($flags & self::HEADER_NO_MIXED_TYPE) {
			$type_flags |= self::TYPE_NO_MIXED;
		}
		if ($flags & self::HEADER_CLASSES_SHORT_NAMES) {
			$type_flags |= self::TYPE_CLASS_SHORT_NAME;
		}
		if ($flags & self::HEADER_CLASSES_LEADING_SLASH) {
			$type_flags |= self::TYPE_CLASS_LEADING_SLASH;
		}
		$type = self::type($function, $type_flags);
		
		//header
		$header = 'function ';
		if (!empty($modifiers)) {
			$header = implode(' ', $modifiers) . ' ' . $header;
		}
		if (isset($name)) {
			$header .= $name;
		}
		$header .= '(' . implode(', ', $parameters) . ')';
		if ($type !== '') {
			$header .= " : {$type}";
		}
		return $header;
	}
	
	/**
	 * Retrieve body from a given function.
	 * 
	 * The returning body from the given function is its PHP code.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @return string <p>The body from the given function.</p>
	 */
	final public static function body(callable $function) : string
	{
		return self::memoize(function () use ($function) : string {
			//initialize
			$reflection = self::reflection($function);
			$filepath = $reflection->getFileName();
			if ($filepath === false) {
				return '';
			}
			
			//body
			$start_line = $reflection->getStartLine();
			$end_line = $reflection->getEndLine();
			$body = implode("\n", array_slice(file($filepath, FILE_IGNORE_NEW_LINES), $start_line - 1, $end_line - $start_line + 1));
			$body = preg_replace(['/^[^{]+\{(.*)\}.*$/sm', '/^(\s*\n)+|(\n\s*)+$/'], ['$1', ''], $body);
			if (preg_match('/^\s+/', $body, $matches)) {
				$body = preg_replace('/^' . preg_quote($matches[0], '/') . '/m', '', $body);
			}
			
			//return
			return $body;
		});
	}
	
	/**
	 * Retrieve source from a given function.
	 * 
	 * The returning source from the given function is the entirety of its PHP code (both header and body).
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @param int $flags [default = 0x00] <p>The source bitwise flags, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CONSTANTS_VALUES</code> : Return the parameters constants values instead of their names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CLASSES_SHORT_NAMES</code> : Return short names for type and parameters classes instead of full namespaced names.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_CLASSES_LEADING_SLASH</code> : Return classes with the leading slash.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SOURCE_NO_MIXED_TYPE</code> : Do not return a mixed type nor parameters with the <code>mixed</code> type keyword.
	 * </p>
	 * @return string <p>The source from the given function.</p>
	 */
	final public static function source(callable $function, int $flags = 0x00) : string
	{
		return self::memoize(function () use ($function, $flags) : string {
			//initialize header
			$header_flags = 0x00;
			if ($flags & self::SOURCE_CONSTANTS_VALUES) {
				$header_flags |= self::HEADER_CONSTANTS_VALUES;
			}
			if ($flags & self::SOURCE_CLASSES_SHORT_NAMES) {
				$header_flags |= self::HEADER_CLASSES_SHORT_NAMES;
			}
			if ($flags & self::SOURCE_CLASSES_LEADING_SLASH) {
				$header_flags |= self::HEADER_CLASSES_LEADING_SLASH;
			}
			if ($flags & self::SOURCE_NO_MIXED_TYPE) {
				$header_flags |= self::HEADER_NO_MIXED_TYPE;
			}
			$header = self::header($function, $header_flags);
			
			//initialize body
			$body = Text::indentate(self::body($function));
			
			//return
			return "{$header}\n{\n{$body}\n}";
		});
	}
	
	/**
	 * Retrieve signature from a given function.
	 * 
	 * The returning signature from the given function is represented only by its parameters and return types.<br>
	 * The expected return format is as follows:<br>
	 * <samp>(parameter1_type, parameter2_type, ...) : return_type</samp>
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @return string <p>The signature from the given function.</p>
	 */
	final public static function signature(callable $function) : string
	{
		return self::memoize(function () use ($function) : string {
			//initialize
			$reflection = self::reflection($function);
			
			//parameter types
			$parameter_types = [];
			foreach ($reflection->getParameters() as $parameter) {
				$parameter_type = 'mixed';
				$ptype = $parameter->getType();
				if (isset($ptype)) {
					$parameter_type = (string)$ptype;
					if ($ptype->allowsNull()) {
						$parameter_type = "?{$parameter_type}";
					}
				}
				if ($parameter->isPassedByReference()) {
					$parameter_type = "&{$parameter_type}";
				}
				if ($parameter->isVariadic()) {
					$parameter_type = "...{$parameter_type}";
				}
				$parameter_types[] = $parameter_type;
			}
			$signature = '(' . implode(', ', $parameter_types) . ')';
			
			//return type
			$return_type = 'mixed';
			$rtype = $reflection->getReturnType();
			if (isset($rtype)) {
				$return_type = (string)$rtype;
				if ($rtype->allowsNull()) {
					$return_type = "?{$return_type}";
				}
			}
			$signature .= " : {$return_type}";
			
			//return
			return $signature;
		});
	}
	
	/**
	 * Assert a given function against a given template.
	 * 
	 * This assertion is only performed in a debug environment.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to assert.</p>
	 * @param callable $template <p>The template callable declaration to assert against.</p>
	 * @param bool $throw_exception [default = false] <p>Throw an exception if the assertion fails.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\SignatureAssertionFailed
	 * @return bool <p>Boolean <samp>true</samp> if the assertion succeeded with the given function against the given template.</p>
	 */
	final public static function assertSignature(callable $function, callable $template, bool $throw_exception = false) : bool
	{
		if (System::getEnvironment()->isDebug()) {
			$function_signature = self::signature($function);
			$template_signature = self::signature($template);
			if ($function_signature !== $template_signature) {
				if ($throw_exception) {
					throw new Exceptions\SignatureAssertionFailed([
						'function' => $function,
						'template' => $template,
						'object_class' => self::stackPreviousObjectClass(),
						'function_signature' => $function_signature,
						'template_signature' => $template_signature
					]);
				}
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Retrieve extension from a given function.
	 * 
	 * @since 1.0.0
	 * @param callable $function <p>The function to retrieve from.</p>
	 * @return string <p>The extension from the given function or <samp>null</samp> if the function does not belong to any extension.</p>
	 */
	final public static function extension(callable $function) : ?string
	{
		$extension = self::reflection($function)->getExtensionName();
		return $extension === false ? null : $extension;
	}
	
	/**
	 * Evaluate a given value as a callable.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param callable|null $template [default = null] <p>The template callable declaration to validate the signature against.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given value is successfully evaluated into a callable.</p>
	 */
	final public static function evaluate(&$value, ?callable $template = null, bool $nullable = false) : bool
	{
		if (!isset($value)) {
			return $nullable;
		} elseif (is_callable($value) && (!isset($template) || self::signature($value) === self::signature($template))) {
			$value = \Closure::fromCallable($value);
			return true;
		}
		return false;
	}
	
	/**
	 * Coerce a given value into a callable.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param callable|null $template [default = null] <p>The template callable declaration to validate the signature against.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\CoercionFailed
	 * @return callable|null <p>The given value coerced into a callable.<br>
	 * If nullable, <samp>null</samp> may also be returned.</p>
	 */
	final public static function coerce($value, ?callable $template = null, bool $nullable = false) : ?callable
	{
		if (!self::evaluate($value, $template, $nullable)) {
			throw new Exceptions\CoercionFailed(['value' => $value, 'template' => $template]);
		}
		return $value;
	}
	
	/**
	 * Retrieve previous class from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0] <p>The stack offset to retrieve from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackOffset
	 * @return string|null <p>The previous class from the current stack or <samp>null</samp> if the previous call in the stack was not called from a class.</p>
	 */
	final public static function stackPreviousClass(int $offset = 0) : ?string
	{
		if ($offset < 0) {
			throw new Exceptions\InvalidStackOffset(['offset' => $offset]);
		}
		return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, $offset + 3)[$offset + 2]['class'] ?? null;
	}
	
	/**
	 * Retrieve previous classes from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0] <p>The stack offset to retrieve from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $limit [default = null] <p>The stack limit on the number of classes to retrieve from.<br>
	 * If not set, no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackOffset
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackLimit
	 * @return string[] <p>The previous classes from the current stack.</p>
	 */
	final public static function stackPreviousClasses(int $offset = 0, ?int $limit = null) : array
	{
		//offset
		if ($offset < 0) {
			throw new Exceptions\InvalidStackOffset(['offset' => $offset]);
		}
		
		//limit
		if (isset($limit)) {
			if ($limit <= 0) {
				throw new Exceptions\InvalidStackLimit(['limit' => $limit]);
			}
			$limit += $offset + 2;
		} else {
			$limit = 0;
		}
	
		//return
		return array_column(array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit), $offset + 2), 'class');
	}
	
	/**
	 * Retrieve previous object from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0] <p>The stack offset to retrieve from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackOffset
	 * @return object|null <p>The previous object from the current stack or <samp>null</samp> if the previous call in the stack was not called from an object.</p>
	 */
	final public static function stackPreviousObject(int $offset = 0)
	{
		if ($offset < 0) {
			throw new Exceptions\InvalidStackOffset(['offset' => $offset]);
		}
		return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, $offset + 3)[$offset + 2]['object'] ?? null;
	}
	
	/**
	 * Retrieve previous objects from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0] <p>The stack offset to retrieve from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $limit [default = null] <p>The stack limit on the number of objects to retrieve from.<br>
	 * If not set, no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackOffset
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackLimit
	 * @return object[] <p>The previous objects from the current stack.</p>
	 */
	final public static function stackPreviousObjects(int $offset = 0, ?int $limit = null) : array
	{
		//offset
		if ($offset < 0) {
			throw new Exceptions\InvalidStackOffset(['offset' => $offset]);
		}
		
		//limit
		if (isset($limit)) {
			if ($limit <= 0) {
				throw new Exceptions\InvalidStackLimit(['limit' => $limit]);
			}
			$limit += $offset + 2;
		} else {
			$limit = 0;
		}
		
		//return
		return array_column(array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit), $offset + 2), 'object');
	}
	
	/**
	 * Retrieve previous object or class from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0] <p>The stack offset to retrieve from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackOffset
	 * @return object|string|null <p>The previous object or class from the current stack or <samp>null</samp> if the previous call in the stack was not called from an object nor a class.</p>
	 */
	final public static function stackPreviousObjectClass(int $offset = 0)
	{
		if ($offset < 0) {
			throw new Exceptions\InvalidStackOffset(['offset' => $offset]);
		}
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, $offset + 3)[$offset + 2] ?? null;
		return isset($backtrace) ? ($backtrace['object'] ?? $backtrace['class'] ?? null) : null;
	}
	
	/**
	 * Retrieve previous objects and classes from the current stack.
	 * 
	 * @since 1.0.0
	 * @param int $offset [default = 0] <p>The stack offset to retrieve from.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $limit [default = null] <p>The stack limit on the number of objects and classes to retrieve from.<br>
	 * If not set, no limit is applied, otherwise it must be greater than <code>0</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackOffset
	 * @throws \Feralygon\Kit\Core\Utilities\Call\Exceptions\InvalidStackLimit
	 * @return object[]|string[] <p>The previous objects and classes from the current stack.</p>
	 */
	final public static function stackPreviousObjectsClasses(int $offset = 0, ?int $limit = null) : array
	{
		//offset
		if ($offset < 0) {
			throw new Exceptions\InvalidStackOffset(['offset' => $offset]);
		}
		
		//limit
		if (isset($limit)) {
			if ($limit <= 0) {
				throw new Exceptions\InvalidStackLimit(['limit' => $limit]);
			}
			$limit += $offset + 2;
		} else {
			$limit = 0;
		}
		
		//objects and classes
		$objects_classes = [];
		foreach (array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit), $offset + 2) as $backtrace) {
			if (isset($backtrace['object'])) {
				$objects_classes[] = $backtrace['object'];
			} elseif (isset($backtrace['class'])) {
				$objects_classes[] = $backtrace['class'];
			}
		}
		
		//return
		return $objects_classes;
	}
}
