<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Utilities\Data\Exceptions;
use Dracodeum\Kit\Interfaces\Arrayable as IArrayable;

/**
 * This utility implements a set of methods used to manipulate data structures in the form of PHP arrays.
 * 
 * @see https://php.net/manual/en/language.types.array.php
 */
final class Data extends Utility
{
	//Public constants
	/** Associative union merge (flag). */
	public const MERGE_ASSOC_UNION = 0x01;
	
	/** Associative left merge (flag). */
	public const MERGE_ASSOC_LEFT = 0x02;
	
	/** Non-associative associative merge (flag). */
	public const MERGE_NONASSOC_ASSOC = 0x04;
	
	/** Non-associative union merge (flag). */
	public const MERGE_NONASSOC_UNION = 0x08;
	
	/** Non-associative left merge (flag). */
	public const MERGE_NONASSOC_LEFT = 0x10;
	
	/** Non-associative swap merge (flag). */
	public const MERGE_NONASSOC_SWAP = 0x20;
	
	/** Non-associative keep merge (flag). */
	public const MERGE_NONASSOC_KEEP = 0x40;
	
	/** Non-associative unique merge (flag). */
	public const MERGE_NONASSOC_UNIQUE = 0x80;
	
	/** Union merge (flag). */
	public const MERGE_UNION = self::MERGE_ASSOC_UNION | self::MERGE_NONASSOC_UNION;
	
	/** Left merge (flag). */
	public const MERGE_LEFT = self::MERGE_ASSOC_LEFT | self::MERGE_NONASSOC_LEFT;
	
	/** Associative exclude unique (flag). */
	public const UNIQUE_ASSOC_EXCLUDE = 0x01;
	
	/** Non-associative associative unique (flag). */
	public const UNIQUE_NONASSOC_ASSOC = 0x02;
	
	/** Non-associative exclude unique (flag). */
	public const UNIQUE_NONASSOC_EXCLUDE = 0x04;
	
	/** Unique associative arrays (flag). */
	public const UNIQUE_ASSOC_ARRAYS = 0x08;
	
	/** Unique non-associative arrays (flag). */
	public const UNIQUE_NONASSOC_ARRAYS = 0x10;
	
	/** Unique arrays as values (flag). */
	public const UNIQUE_ARRAYS_AS_VALUES = 0x20;
	
	/** Unique arrays (flag). */
	public const UNIQUE_ARRAYS = self::UNIQUE_ASSOC_ARRAYS | self::UNIQUE_NONASSOC_ARRAYS;
	
	/** Reverse sort (flag). */
	public const SORT_REVERSE = 0x01;
	
	/** Associative exclude sort (flag). */
	public const SORT_ASSOC_EXCLUDE = 0x04;
	
	/** Non-associative associative sort (flag). */
	public const SORT_NONASSOC_ASSOC = 0x08;
	
	/** Non-associative exclude sort (flag). */
	public const SORT_NONASSOC_EXCLUDE = 0x10;
	
	/** Inverse filter (flag). */
	public const FILTER_INVERSE = 0x01;
	
	/** Empty filter (flag). */
	public const FILTER_EMPTY = 0x02;
	
	/** Associative exclude filter (flag). */
	public const FILTER_ASSOC_EXCLUDE = 0x04;
	
	/** Non-associative associative filter (flag). */
	public const FILTER_NONASSOC_ASSOC = 0x08;
	
	/** Non-associative exclude filter (flag). */
	public const FILTER_NONASSOC_EXCLUDE = 0x10;
	
	/** Inverse trim (flag). */
	public const TRIM_INVERSE = 0x01;
	
	/** Left trim (flag). */
	public const TRIM_LEFT = 0x02;
	
	/** Right trim (flag). */
	public const TRIM_RIGHT = 0x04;
	
	/** Empty trim (flag). */
	public const TRIM_EMPTY = 0x08;
	
	/** Associative exclude trim (flag). */
	public const TRIM_ASSOC_EXCLUDE = 0x10;
	
	/** Non-associative associative trim (flag). */
	public const TRIM_NONASSOC_ASSOC = 0x20;
	
	/** Non-associative exclude trim (flag). */
	public const TRIM_NONASSOC_EXCLUDE = 0x40;
	
	/** Associative exclude intersection (flag). */
	public const INTERSECT_ASSOC_EXCLUDE = 0x01;
	
	/** Non-associative associative intersection (flag). */
	public const INTERSECT_NONASSOC_ASSOC = 0x02;
	
	/** Non-associative exclude intersection (flag). */
	public const INTERSECT_NONASSOC_EXCLUDE = 0x04;

	/** Associative exclude difference (flag). */
	public const DIFF_ASSOC_EXCLUDE = 0x01;
	
	/** Non-associative associative difference (flag). */
	public const DIFF_NONASSOC_ASSOC = 0x02;
	
	/** Non-associative exclude difference (flag). */
	public const DIFF_NONASSOC_EXCLUDE = 0x04;

	/** Associative exclude shuffle (flag). */
	public const SHUFFLE_ASSOC_EXCLUDE = 0x01;
	
	/** Non-associative associative shuffle (flag). */
	public const SHUFFLE_NONASSOC_ASSOC = 0x02;
	
	/** Non-associative exclude shuffle (flag). */
	public const SHUFFLE_NONASSOC_EXCLUDE = 0x04;
	
	/** Associative exclude alignment (flag). */
	public const ALIGN_ASSOC_EXCLUDE = 0x01;
	
	/** Non-associative exclude alignment (flag). */
	public const ALIGN_NONASSOC_EXCLUDE = 0x02;
	
	/** Associative exclude collapse (flag). */
	public const COLLAPSE_ASSOC_EXCLUDE = 0x01;
	
	/** Non-associative exclude collapse (flag). */
	public const COLLAPSE_NONASSOC_EXCLUDE = 0x02;
	
	
	
	//Private constants	
	/** Keyfy maximum raw string length before converting into a hash. */
	private const KEYFY_MAX_RAW_STRING_LENGTH = 40;
	
	/** Keyfy maximum raw array length before converting into a hash. */
	private const KEYFY_MAX_RAW_ARRAY_LENGTH = 40;
	
	
	
	//Final public static methods
	/**
	 * Check if a given array is associative.
	 * 
	 * The given array is only considered to be associative when it's not empty and 
	 * its keys are not consecutive integers starting from <code>0</code>.
	 * 
	 * @param array $array
	 * <p>The array to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given array is associative.</p>
	 */
	final public static function associative(array $array): bool
	{
		return !empty($array) && (
			!array_key_exists(0, $array) || 
			!array_key_exists(count($array) - 1, $array) || 
			array_keys($array) !== range(0, count($array) - 1)
		);
	}
	
	/**
	 * Convert a given value into a unique key.
	 * 
	 * The returning key is not intended to be restored to its original value (and cannot in most cases), 
	 * given that this function is only meant to efficiently produce a key which can be used in associative arrays 
	 * for strict mapping and data comparisons.
	 * 
	 * @param mixed $value
	 * <p>The value to convert.</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the generated key may be used for longer term purposes, such as internal cache keys.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\Keyfy\UnsupportedValueType
	 * @return string|null
	 * <p>A unique key from the given value.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, then <code>null</code> is returned if it failed.</p>
	 */
	final public static function keyfy($value, ?bool &$safe = null, bool $no_throw = false): ?string
	{
		$safe = null;
		if (!isset($value)) {
			$safe = true;
			return 'n';
		} elseif (is_bool($value)) {
			$safe = true;
			return 'b:' . ($value ? '1' : '0');
		} elseif (is_int($value)) {
			$safe = true;
			return "i:{$value}";
		} elseif (is_float($value)) {
			$safe = true;
			return "f:{$value}";
		} elseif (is_string($value)) {
			$safe = true;
			return strlen($value) > self::KEYFY_MAX_RAW_STRING_LENGTH ? 'S:' . sha1($value) : "s:{$value}";
		} elseif (is_object($value)) {
			$safe = false;
			return 'O:' . spl_object_id($value);
		} elseif (is_resource($value)) {
			$safe = false;
			return 'R:' . (int)$value;
		} elseif (is_array($value)) {
			$array_safe = true;
			foreach ($value as &$v) {
				$v = self::keyfy($v, $s, $no_throw);
				if (!isset($v)) {
					return null;
				}
				$array_safe = $array_safe && $s;
			}
			unset($v);
			$safe = $array_safe;
			$value = json_encode($value);
			return strlen($value) > self::KEYFY_MAX_RAW_ARRAY_LENGTH ? 'A:' . sha1($value) : "a:{$value}";
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\Keyfy\UnsupportedValueType([$value]);
	}
	
	/**
	 * Merge two given arrays recursively.
	 * 
	 * By omission, non-associative arrays are joined together and their keys are recalculated, 
	 * whereas with associative arrays all the keys from the second array are inserted into the first one, 
	 * which are replaced if already existent.
	 * 
	 * @see https://php.net/manual/en/function.array-merge.php
	 * @param array $array1
	 * <p>The first array, to merge into.</p>
	 * @param array $array2
	 * <p>The second array, to merge with.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the merging at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_ASSOC_UNION</code> : 
	 * Merge associative arrays using the union operation, in other words,
	 * with this flag keys present in the first array won't be replaced by the same keys present in the second.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_ASSOC_LEFT</code> : 
	 * Merge associative arrays but only from the left, in other words,
	 * with this flag only the keys present in the first array will remain, 
	 * while any keys exclusively present in the second will be discarded.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_NONASSOC_ASSOC</code> : 
	 * Merge non-associative arrays associatively.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_NONASSOC_UNION</code> : 
	 * Merge non-associative arrays associatively by using the union operation, in other words,
	 * with this flag keys present in the first array won't be replaced by the same keys present 
	 * in the second for non-associative arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_NONASSOC_LEFT</code> : 
	 * Merge non-associative arrays associatively but only from the left, in other words,
	 * with this flag only the keys present in the first array will remain, 
	 * while any keys exclusively present in the second will be discarded for non-associative arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_NONASSOC_SWAP</code> : 
	 * Merge non-associative arrays by swapping the second entirely with the first.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_NONASSOC_KEEP</code> : 
	 * Merge non-associative arrays by keeping the first entirely.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::MERGE_NONASSOC_UNIQUE</code> : 
	 * When merging non-associative arrays, ensure that only unique values are present in the merged array.</p>
	 * @return array
	 * <p>The merged array from the two given ones.</p>
	 */
	final public static function merge(array $array1, array $array2, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//initialize
		$is_assoc = self::associative($array1) || self::associative($array2);
		$is_union = ($is_assoc && ($flags & self::MERGE_ASSOC_UNION)) || 
			(!$is_assoc && ($flags & self::MERGE_NONASSOC_UNION));
		$is_left = ($is_assoc && ($flags & self::MERGE_ASSOC_LEFT)) || 
			(!$is_assoc && ($flags & self::MERGE_NONASSOC_LEFT));
		$is_unique = !$is_assoc && ($flags & self::MERGE_NONASSOC_UNIQUE);
		$unique_flags = ($flags & self::MERGE_NONASSOC_ASSOC) ? self::UNIQUE_NONASSOC_ASSOC : 0x00;
		
		//non-associative
		$non_assoc_flags = self::MERGE_NONASSOC_ASSOC | self::MERGE_NONASSOC_UNION | self::MERGE_NONASSOC_LEFT;
		if (!$is_assoc && !($flags & $non_assoc_flags)) {
			$array = [];
			if ($flags & self::MERGE_NONASSOC_SWAP) {
				$array = $array2;
			} elseif ($flags & self::MERGE_NONASSOC_KEEP) {
				$array = $array1;
			} else {
				$array = array_merge($array1, $array2);
			}
			return $is_unique ? self::unique($array, 0, $unique_flags) : $array;
		}
		
		//empty
		if (empty($array1) || empty($array2)) {
			if (empty($array1)) {
				return $is_left ? [] : ($is_unique ? self::unique($array2, 0, $unique_flags) : $array2);
			}
			return $is_unique ? self::unique($array1, 0, $unique_flags) : $array1;
		}
		
		//union
		if ($is_union && $depth === 0) {
			$array = $array1;
			if (!$is_left) {
				$array += $array2;
			}
			return $is_unique ? self::unique($array, 0, $unique_flags) : $array;
		}
		
		//merge
		$array = $array1;
		$next_depth = isset($depth) ? $depth - 1 : null;
		foreach ($array as $k => &$v) {
			if (array_key_exists($k, $array2)) {
				if (is_array($v) && is_array($array2[$k]) && (!isset($next_depth) || $next_depth >= 0)) {
					$v = self::merge($v, $array2[$k], $next_depth, $flags);
				} elseif (!$is_union) {
					$v = $array2[$k];
				}
			}
		}
		unset($v);
		
		//finish
		if (!$is_left) {
			$array += $array2;
		}
		if ($is_unique) {
			$array = self::unique($array, 0, $unique_flags);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Remove duplicated non-array values from a given array strictly and recursively.
	 * 
	 * The removal is performed in such a way that only strictly unique values are present in the returning array, 
	 * as not only the values are considered, but also their types as well.<br>
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact.<br>
	 * <br>
	 * Since the function is recursive and only handles non-array values, 
	 * values which are themselves arrays are not affected, 
	 * therefore it's possible to have two or more arrays with exactly the same data in the returning array.
	 * 
	 * @see https://php.net/manual/en/function.array-unique.php
	 * @param array $array
	 * <p>The array to remove from.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the removal at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNIQUE_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from the removal of duplicates.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNIQUE_NONASSOC_ASSOC</code> : 
	 * Remove duplicates from non-associative arrays associatively, in other words, keep the keys intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNIQUE_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from the removal of duplicates.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNIQUE_ASSOC_ARRAYS</code> : 
	 * Remove duplicated associative arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNIQUE_NONASSOC_ARRAYS</code> : 
	 * Remove duplicated non-associative arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNIQUE_ARRAYS_AS_VALUES</code> : 
	 * Handle arrays as values, at the last recursion depth only.</p>
	 * @return array
	 * <p>The given array without duplicated values.</p>
	 */
	final public static function unique(array $array, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//initialize
		$is_assoc = self::associative($array);
		$is_included = ($is_assoc && !($flags & self::UNIQUE_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::UNIQUE_NONASSOC_EXCLUDE));
		
		//unique
		if ($is_included) {
			$map = [];
			$is_arrays_as_values = ($flags & self::UNIQUE_ARRAYS_AS_VALUES) && $depth === 0;
			foreach ($array as $k => $v) {
				$key = is_array($v) && !$is_arrays_as_values ? "a:{$k}" : self::keyfy($v);
				if (isset($map[$key])) {
					unset($array[$k]);
				} else {
					$map[$key] = true;
				}
			}
			unset($map);
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as &$v) {
				if (is_array($v)) {
					$v = self::unique($v, $next_depth, $flags);
				}
			}
			unset($v);
		}
		
		//arrays
		if ($is_included && ($flags & self::UNIQUE_ARRAYS)) {
			$map = [];
			$is_assoc_arrays = (bool)($flags & self::UNIQUE_ASSOC_ARRAYS);
			$is_nonassoc_arrays = (bool)($flags & self::UNIQUE_NONASSOC_ARRAYS);
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					$is_array_assoc = ($flags & self::UNIQUE_NONASSOC_ASSOC) || self::associative($v);
					if (($is_array_assoc && $is_assoc_arrays) || (!$is_array_assoc && $is_nonassoc_arrays)) {
						$key = self::keyfy($v);
						if (isset($map[$key])) {
							unset($array[$k]);
						} else {
							$map[$key] = true;
						}
					}
				}
			}
			unset($map);
		}
		
		//non-associative
		if (!$is_assoc && !($flags & self::UNIQUE_NONASSOC_ASSOC)) {
			$array = array_values($array);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Sort a given array recursively.
	 * 
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact, and the sorting is performed in ascending order.
	 * 
	 * @see https://php.net/manual/en/function.sort.php
	 * @see https://php.net/manual/en/function.rsort.php
	 * @see https://php.net/manual/en/function.asort.php
	 * @see https://php.net/manual/en/function.arsort.php
	 * @param array $array
	 * <p>The array to sort.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the sorting at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_REVERSE</code> : 
	 * Sort array in reverse (descending order).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from sorting.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_NONASSOC_ASSOC</code> : 
	 * Sort non-associative arrays associatively, in other words, keep the keys intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from sorting.</p>
	 * @return array
	 * <p>The sorted array.</p>
	 */
	final public static function sort(array $array, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//sort
		$is_assoc = self::associative($array);
		if (
			($is_assoc && !($flags & self::SORT_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::SORT_NONASSOC_EXCLUDE))
		) {
			if ($is_assoc || ($flags & self::SORT_NONASSOC_ASSOC)) {
				if ($flags & self::SORT_REVERSE) {
					arsort($array);
				} else {
					asort($array);
				}
			} else {
				if ($flags & self::SORT_REVERSE) {
					rsort($array);
				} else {
					sort($array);
				}
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as &$v) {
				if (is_array($v)) {
					$v = self::sort($v, $next_depth, $flags);
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Sort a given array recursively by key.
	 * 
	 * By omission, the sorting is performed in ascending order.
	 * 
	 * @see https://php.net/manual/en/function.ksort.php
	 * @see https://php.net/manual/en/function.krsort.php
	 * @param array $array
	 * <p>The array to sort.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the sorting at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_REVERSE</code> : 
	 * Sort array in reverse (descending order).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from sorting.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from sorting.</p>
	 * @return array
	 * <p>The sorted array by key.</p>
	 */
	final public static function ksort(array $array, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//sort
		$is_assoc = self::associative($array);
		if (
			($is_assoc && !($flags & self::SORT_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::SORT_NONASSOC_EXCLUDE))
		) {
			if ($flags & self::SORT_REVERSE) {
				krsort($array);
			} else {
				ksort($array);
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as &$v) {
				if (is_array($v)) {
					$v = self::ksort($v, $next_depth, $flags);
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Sort a given array recursively using a given comparer function.
	 * 
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact, and the sorting is performed in ascending order.
	 * 
	 * @see https://php.net/manual/en/function.usort.php
	 * @see https://php.net/manual/en/function.uasort.php
	 * @see https://php.net/manual/en/function.uksort.php
	 * @param array $array
	 * <p>The array to sort.</p>
	 * @param callable $comparer
	 * <p>The comparer function to use to compare two key-value pairs.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($key1, $value1, $key2, $value2): int</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key1</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The first key to compare.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value1</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The first value to compare.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key2</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The second key to compare against.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value2</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The second value to compare against.<br>
	 * <br>
	 * Return: <code><b>int</b></code><br>
	 * The difference between the given first key-value pair and the second one.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the sorting at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_REVERSE</code> : 
	 * Sort array in reverse (descending order).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from sorting.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_NONASSOC_ASSOC</code> : 
	 * Sort non-associative arrays associatively, in other words, keep the keys intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SORT_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from sorting.</p>
	 * @return array
	 * <p>The sorted array.</p>
	 */
	final public static function fsort(array $array, callable $comparer, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//comparer
		Call::assert('comparer', $comparer, function ($key1, $value1, $key2, $value2): int {});
		$comparer = \Closure::fromCallable($comparer);
		
		//sort
		$is_assoc = self::associative($array);
		if (
			($is_assoc && !($flags & self::SORT_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::SORT_NONASSOC_EXCLUDE))
		) {
			//sort
			$reverse = (bool)($flags & self::SORT_REVERSE);
			uksort($array, function ($key1, $key2) use ($array, $comparer, $reverse): int {
				$diff = $comparer($key1, $array[$key1], $key2, $array[$key2]);
				return $reverse ? -$diff : $diff;
			});
			
			//non-associative
			if (!$is_assoc && !($flags & self::SORT_NONASSOC_ASSOC)) {
				$array = array_values($array);
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as &$v) {
				if (is_array($v)) {
					$v = self::fsort($v, $comparer, $next_depth, $flags);
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Filter a given array strictly and recursively from a given set of non-array values.
	 * 
	 * The filtering is performed in such a way that the given values are strictly removed from the returning array, 
	 * as not only the values are considered, but also their types as well.<br>
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact.<br>
	 * <br>
	 * Since the function is recursive and only handles non-array values, 
	 * values which are themselves arrays are not affected.
	 * 
	 * @see https://php.net/manual/en/function.array-filter.php
	 * @param array $array
	 * <p>The array to filter.</p>
	 * @param array $values
	 * <p>The values to filter from.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the filtering at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_INVERSE</code> : 
	 * Filter array inversely, in other words, 
	 * strictly filter array from all non-array values but the given ones.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_EMPTY</code> : 
	 * Filter array from empty arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from filtering.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_NONASSOC_ASSOC</code> : 
	 * Filter non-associative arrays associatively, in other words, keep the keys intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from filtering.</p>
	 * @return array
	 * <p>The filtered array from the given set of non-array values.</p>
	 */
	final public static function filter(array $array, array $values, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//filter
		$is_assoc = self::associative($array);
		$is_empty = (bool)($flags & self::FILTER_EMPTY);
		if (
			($is_assoc && !($flags & self::FILTER_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::FILTER_NONASSOC_EXCLUDE))
		) {
			//iterate
			$is_inverse = (bool)($flags & self::FILTER_INVERSE);
			foreach ($array as $k => $v) {
				if ($is_empty && is_array($v) && empty($v)) {
					unset($array[$k]);
				} elseif (!is_array($v) && in_array($v, $values, true) !== $is_inverse) {
					unset($array[$k]);
				}
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as $k => &$v) {
				if (is_array($v)) {
					$v = self::filter($v, $values, $next_depth, $flags);
					if ($is_empty && empty($v)) {
						unset($array[$k]);
					}
				}
			}
			unset($v);
		}
			
		//non-associative
		if (!$is_assoc && !($flags & self::FILTER_NONASSOC_ASSOC)) {
			$array = array_values($array);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Filter a given array recursively from a given set of keys.
	 * 
	 * @param array $array
	 * <p>The array to filter.</p>
	 * @param int[]|string[] $keys
	 * <p>The keys to filter from.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the filtering at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_INVERSE</code> : 
	 * Filter array inversely, in other words, filter array from all keys but the given ones.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_EMPTY</code> : 
	 * Filter array from empty arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from filtering.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::FILTER_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from filtering.</p>
	 * @return array
	 * <p>The filtered array from the given set of keys.</p>
	 */
	final public static function kfilter(array $array, array $keys, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//filter
		$is_assoc = self::associative($array);
		$is_empty = (bool)($flags & self::FILTER_EMPTY);
		if (
			($is_assoc && !($flags & self::FILTER_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::FILTER_NONASSOC_EXCLUDE))
		) {
			$array = ($flags & self::FILTER_INVERSE)
				? array_intersect_key($array, array_flip($keys))
				: array_diff_key($array, array_flip($keys));
			if ($is_empty) {
				foreach ($array as $k => $v) {
					if (is_array($v) && empty($v)) {
						unset($array[$k]);
					}
				}
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as $k => &$v) {
				if (is_array($v)) {
					$v = self::kfilter($v, $keys, $next_depth, $flags);
					if ($is_empty && empty($v)) {
						unset($array[$k]);
					}
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Trim a given array strictly and recursively from a given set of non-array values.
	 * 
	 * The trimming is performed in such a way that the given values are strictly trimmed out from the returning array, 
	 * as not only the values are considered, but also their types as well.<br>
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact.<br>
	 * <br>
	 * Since the function is recursive and only handles non-array values, 
	 * values which are themselves arrays are not affected.
	 * 
	 * @param array $array
	 * <p>The array to trim.</p>
	 * @param array $values
	 * <p>The values to trim from.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the trimming at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_INVERSE</code> : 
	 * Trim array inversely, in other words, strictly trim array from all non-array values but the given ones.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_LEFT</code> : 
	 * Trim only the left side of the array (the first values).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_RIGHT</code> : 
	 * Trim only the right side of the array (the last values).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_EMPTY</code> : 
	 * Trim array from empty arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from trimming.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_NONASSOC_ASSOC</code> : 
	 * Trim non-associative arrays associatively, in other words, keep the keys intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from trimming.</p>
	 * @return array
	 * <p>The trimmed array from the given set of non-array values.</p>
	 */
	final public static function trim(array $array, array $values, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//trim
		$is_assoc = self::associative($array);
		$is_empty = (bool)($flags & self::TRIM_EMPTY);
		if (
			($is_assoc && !($flags & self::TRIM_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::TRIM_NONASSOC_EXCLUDE))
		) {
			//initialize
			if (!($flags & (self::TRIM_LEFT | self::TRIM_RIGHT))) {
				$flags |= self::TRIM_LEFT | self::TRIM_RIGHT;
			}
			$pipe_keys = [];
			$array_keys = array_keys($array);
			if ($flags & self::TRIM_LEFT) {
				$pipe_keys[] = $array_keys;
			}
			if ($flags & self::TRIM_RIGHT) {
				$pipe_keys[] = array_reverse($array_keys);
			}
			
			//iterate
			$is_inverse = (bool)($flags & self::TRIM_INVERSE);
			foreach ($pipe_keys as $pkeys) {
				foreach ($pkeys as $k) {
					$v = $array[$k];
					if (
						(!$is_empty || !is_array($v) || !empty($v)) && 
						(is_array($v) || in_array($v, $values, true) === $is_inverse)
					) {
						break;
					}
					unset($array[$k]);
				}
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as $k => &$v) {
				if (is_array($v)) {
					$v = self::trim($v, $values, $next_depth, $flags);
					if ($is_empty && empty($v)) {
						unset($array[$k]);
					}
				}
			}
			unset($v);
		}
			
		//non-associative
		if (!$is_assoc && !($flags & self::TRIM_NONASSOC_ASSOC)) {
			$array = array_values($array);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Trim a given array recursively from a given set of keys.
	 * 
	 * @param array $array
	 * <p>The array to trim.</p>
	 * @param int[]|string[] $keys
	 * <p>The keys to trim from.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the trimming at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_INVERSE</code> : 
	 * Trim array inversely, in other words, trim array from all keys but the given ones.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_LEFT</code> : 
	 * Trim only the left side of the array (the first keys).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_RIGHT</code> : 
	 * Trim only the right side of the array (the last keys).<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_EMPTY</code> : 
	 * Trim array from empty arrays.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from trimming.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::TRIM_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from trimming.</p>
	 * @return array
	 * <p>The trimmed array from the given set of keys.</p>
	 */
	final public static function ktrim(array $array, array $keys, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//trim
		$is_assoc = self::associative($array);
		$is_empty = (bool)($flags & self::TRIM_EMPTY);
		if (
			($is_assoc && !($flags & self::TRIM_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::TRIM_NONASSOC_EXCLUDE))
		) {
			//initialize
			if (!($flags & (self::TRIM_LEFT | self::TRIM_RIGHT))) {
				$flags |= self::TRIM_LEFT | self::TRIM_RIGHT;
			}
			$pipe_keys = [];
			$array_keys = array_keys($array);
			if ($flags & self::TRIM_LEFT) {
				$pipe_keys[] = $array_keys;
			}
			if ($flags & self::TRIM_RIGHT) {
				$pipe_keys[] = array_reverse($array_keys);
			}
			
			//iterate
			$keys_map = array_flip($keys);
			$is_inverse = (bool)($flags & self::TRIM_INVERSE);
			foreach ($pipe_keys as $pkeys) {
				foreach ($pkeys as $k) {
					if (
						isset($keys_map[$k]) === $is_inverse && 
						(!$is_empty || !is_array($array[$k]) || !empty($array[$k]))
					) {
						break;
					}
					unset($array[$k]);
				}
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as $k => &$v) {
				if (is_array($v)) {
					$v = self::ktrim($v, $keys, $next_depth, $flags);
					if ($is_empty && empty($v)) {
						unset($array[$k]);
					}
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Intersect two given arrays strictly and recursively.
	 * 
	 * The intersection is performed in such a way that the returning array is only composed by the values from 
	 * the first array which also strictly exist in the second one as well, as not only the values are considered, 
	 * but also their types as well.<br>
	 * <br>
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact 
	 * and the keys themselves are also considered for the intersection.
	 * 
	 * @see https://php.net/manual/en/function.array-intersect.php
	 * @param array $array1
	 * <p>The first array, to intersect from.</p>
	 * @param array $array2
	 * <p>The second array, to intersect with.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the intersection at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::INTERSECT_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from intersecting.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::INTERSECT_NONASSOC_ASSOC</code> : 
	 * Intersect non-associative arrays associatively, in other words, 
	 * consider the keys in the intersection and keep them intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::INTERSECT_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from intersecting.</p>
	 * @return array
	 * <p>The intersected array from the two given arrays.</p>
	 */
	final public static function intersect(array $array1, array $array2, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//empty arrays
		if (empty($array1) || empty($array2)) {
			return [];
		}
		
		//intersect
		$is_assoc = self::associative($array1) || self::associative($array2);
		if (
			($is_assoc && !($flags & self::INTERSECT_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::INTERSECT_NONASSOC_EXCLUDE))
		) {
			//associative
			if ($is_assoc || ($flags & self::INTERSECT_NONASSOC_ASSOC)) {
				$array1 = array_intersect_key($array1, $array2);
				foreach ($array1 as $k => $v) {
					if (!is_array($v) && $v !== $array2[$k]) {
						unset($array1[$k]);
					}
				}
				
			//non-associative
			} else {
				//mapping
				$maps = [[], []];
				$arrays = [$array1, $array2];
				foreach ($arrays as $i => $array) {
					foreach ($array as $k => $v) {
						$maps[$i][is_array($v) ? "ak:{$k}" : self::keyfy($v)][$k] = true;
					}
				}
				
				//intersection
				$array = [];
				foreach (array_intersect_key($maps[0], $maps[1]) as $map) {
					$array += array_intersect_key($array1, $map);
				}
				$array1 = array_values($array);
				unset($maps, $arrays, $array);
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array1 as $k => &$v) {
				if (is_array($v)) {
					if (isset($array2[$k]) && is_array($array2[$k])) {
						$v = self::intersect($v, $array2[$k], $next_depth, $flags);
						if (empty($v)) {
							unset($array1[$k]);
						}
					} else {
						unset($array1[$k]);
					}
				}
			}
			unset($v);
		}
			
		//non-associative
		if (!$is_assoc && !($flags & self::INTERSECT_NONASSOC_ASSOC)) {
			$array1 = array_values($array1);
		}
		
		//return
		return $array1;
	}
	
	/**
	 * Intersect two given arrays recursively by key.
	 * 
	 * @see https://php.net/manual/en/function.array-intersect-key.php
	 * @param array $array1
	 * <p>The first array, to intersect from.</p>
	 * @param array $array2
	 * <p>The second array, to intersect with.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the intersection at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::INTERSECT_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from intersecting.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::INTERSECT_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from intersecting.</p>
	 * @return array
	 * <p>The intersected array by key from the two given arrays.</p>
	 */
	final public static function kintersect(array $array1, array $array2, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//empty arrays
		if (empty($array1) || empty($array2)) {
			return [];
		}
		
		//intersect
		$is_assoc = self::associative($array1) || self::associative($array2);
		if (
			($is_assoc && !($flags & self::INTERSECT_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::INTERSECT_NONASSOC_EXCLUDE))
		) {
			$array1 = array_intersect_key($array1, $array2);
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array1 as $k => &$v) {
				if (is_array($v)) {
					if (isset($array2[$k]) && is_array($array2[$k])) {
						$v = self::kintersect($v, $array2[$k], $next_depth, $flags);
						if (empty($v)) {
							unset($array1[$k]);
						}
					} else {
						unset($array1[$k]);
					}
				}
			}
			unset($v);
		}
		
		//return
		return $array1;
	}
	
	/**
	 * Differentiate two given arrays strictly and recursively.
	 * 
	 * The differentiation is performed in such a way that the returning array is only composed by the values 
	 * from the first array which strictly do not exist in the second one, as not only the values are considered, 
	 * but also their types as well.<br>
	 * <br>
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact
	 * and the keys themselves are also considered for the differentiation.
	 * 
	 * @see https://php.net/manual/en/function.array-diff.php
	 * @param array $array1
	 * <p>The first array, to differentiate from.</p>
	 * @param array $array2
	 * <p>The second array, to differentiate with.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the differentiation at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::DIFF_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from differentiating.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::DIFF_NONASSOC_ASSOC</code> : 
	 * Differentiate non-associative arrays associatively, in other words, 
	 * consider the keys in the difference and keep them intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::DIFF_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from differentiating.</p>
	 * @return array
	 * <p>The differentiated array from the two given arrays.</p>
	 */
	final public static function diff(array $array1, array $array2, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//empty arrays
		if (empty($array1) || empty($array2)) {
			return $array1;
		}
		
		//differentiate
		$is_assoc = self::associative($array1) || self::associative($array2);
		if (
			($is_assoc && !($flags & self::DIFF_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::DIFF_NONASSOC_EXCLUDE))
		) {
			//associative
			if ($is_assoc || ($flags & self::DIFF_NONASSOC_ASSOC)) {
				foreach ($array1 as $k => $v) {
					if (array_key_exists($k, $array2) && !is_array($v) && $v === $array2[$k]) {
						unset($array1[$k]);
					}
				}
				
			//non-associative
			} else {
				//mapping
				$maps = [[], []];
				$arrays = [$array1, $array2];
				foreach ($arrays as $i => $array) {
					foreach ($array as $k => $v) {
						$maps[$i][is_array($v) ? "ak:{$i}:{$k}" : self::keyfy($v)][$k] = true;
					}
				}
				
				//difference
				$array = [];
				foreach (array_diff_key($maps[0], $maps[1]) as $map) {
					$array += array_intersect_key($array1, $map);
				}
				$array1 = array_values($array);
				unset($maps, $arrays, $array);
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array1 as $k => &$v) {
				if (is_array($v) && isset($array2[$k]) && is_array($array2[$k])) {
					$v = self::diff($v, $array2[$k], $next_depth, $flags);
					if (empty($v)) {
						unset($array1[$k]);
					}
				}
			}
			unset($v);
		}
			
		//non-associative
		if (!$is_assoc && !($flags & self::DIFF_NONASSOC_ASSOC)) {
			$array1 = array_values($array1);
		}
		
		//return
		return $array1;
	}
	
	/**
	 * Differentiate two given arrays recursively by key.
	 * 
	 * @see https://php.net/manual/en/function.array-diff-key.php
	 * @param array $array1
	 * <p>The first array, to differentiate from.</p>
	 * @param array $array2
	 * <p>The second array, to differentiate with.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the differentiation at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::DIFF_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from differentiating.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::DIFF_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from differentiating.</p>
	 * @return array
	 * <p>The differentiated array by key from the two given arrays.</p>
	 */
	final public static function kdiff(array $array1, array $array2, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//empty arrays
		if (empty($array1) || empty($array2)) {
			return $array1;
		}
		
		//differentiate
		$is_assoc = self::associative($array1) || self::associative($array2);
		if (
			($is_assoc && !($flags & self::DIFF_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::DIFF_NONASSOC_EXCLUDE))
		) {
			foreach ($array1 as $k => $v) {
				if (array_key_exists($k, $array2) && (!is_array($v) || !is_array($array2[$k]))) {
					unset($array1[$k]);
				}
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array1 as $k => &$v) {
				if (is_array($v) && isset($array2[$k]) && is_array($array2[$k])) {
					$v = self::kdiff($v, $array2[$k], $next_depth, $flags);
					if (empty($v)) {
						unset($array1[$k]);
					}
				}
			}
			unset($v);
		}
		
		//return
		return $array1;
	}
	
	/**
	 * Shuffle a given array recursively.
	 * 
	 * By omission, in non-associative arrays the keys are recalculated, 
	 * whereas in associative arrays the keys are kept intact.
	 * 
	 * @see https://php.net/manual/en/function.shuffle.php
	 * @param array $array
	 * <p>The array to shuffle.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the shuffling at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::SHUFFLE_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from shuffling.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SHUFFLE_NONASSOC_ASSOC</code> : 
	 * Shuffle non-associative arrays associatively, in other words, keep the keys intact.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::SHUFFLE_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from shuffling.</p>
	 * @return array
	 * <p>The shuffled array.</p>
	 */
	final public static function shuffle(array $array, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//shuffle
		$is_assoc = self::associative($array);
		if (
			($is_assoc && !($flags & self::SHUFFLE_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::SHUFFLE_NONASSOC_EXCLUDE))
		) {
			if ($is_assoc || ($flags & self::SHUFFLE_NONASSOC_ASSOC)) {
				$keys = array_keys($array);
				shuffle($keys);
				$array = self::align($array, $keys, 0);
			} else {
				shuffle($array);
			}
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as &$v) {
				if (is_array($v)) {
					$v = self::shuffle($v, $next_depth, $flags);
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Align a given array with a given set of keys recursively.
	 * 
	 * @param array $array
	 * <p>The array to align.</p>
	 * @param int[]|string[] $keys
	 * <p>The keys to align with.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the alignment at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::ALIGN_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from aligning.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::ALIGN_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from aligning.</p>
	 * @return array
	 * <p>The aligned array with the given set of keys.</p>
	 */
	final public static function align(array $array, array $keys, ?int $depth = null, int $flags = 0x00): array
	{
		//guard
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//align
		$is_assoc = self::associative($array);
		if (
			($is_assoc && !($flags & self::ALIGN_ASSOC_EXCLUDE)) || 
			(!$is_assoc && !($flags & self::ALIGN_NONASSOC_EXCLUDE))
		) {
			$alignment = [];
			foreach ($keys as $key) {
				if (array_key_exists($key, $array)) {
					$alignment[$key] = $array[$key];
				}
			}
			$array = $alignment + $array;
			unset($alignment);
		}
		
		//recursion
		if ($depth !== 0) {
			$next_depth = isset($depth) ? $depth - 1 : null;
			foreach ($array as &$v) {
				if (is_array($v)) {
					$v = self::align($v, $keys, $next_depth, $flags);
				}
			}
			unset($v);
		}
		
		//return
		return $array;
	}
	
	/**
	 * Check if a given array has a given path.
	 * 
	 * A path is recognized as <samp>key1 + delimiter + key2 + delimiter + ...</samp>, like so:<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> is equivalent to <code>$array['foo']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar</samp> is equivalent to <code>$array['foo']['bar']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar.123</samp> is equivalent to <code>$array['foo']['bar'][123]</code>.
	 * 
	 * @param array $array
	 * <p>The array to check in.</p>
	 * @param string $path
	 * <p>The path to check for.</p>
	 * @param string $delimiter [default = '.']
	 * <p>The path delimiter character to use.<br>
	 * It must be a single ASCII character.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given array has the given path.</p>
	 */
	final public static function has(array $array, string $path, string $delimiter = '.'): bool
	{
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		
		//check
		$pointer = $array;
		foreach (explode($delimiter, $path) as $key) {
			if (!is_array($pointer) || !array_key_exists($key, $pointer)) {
				return false;
			}
			$pointer = $pointer[$key];
		}
		return true;
	}
	
	/**
	 * Get value from a given array at a given path.
	 * 
	 * A path is recognized as <samp>key1 + delimiter + key2 + delimiter + ...</samp>, like so:<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> is equivalent to <code>$array['foo']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar</samp> is equivalent to <code>$array['foo']['bar']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar.123</samp> is equivalent to <code>$array['foo']['bar'][123]</code>.
	 * 
	 * @param array $array
	 * <p>The array to get from.</p>
	 * @param string $path
	 * <p>The path to get from.</p>
	 * @param string $delimiter [default = '.']
	 * <p>The path delimiter character to use.<br>
	 * It must be a single ASCII character.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\PathNotFound
	 * @return mixed
	 * <p>The value from the given array at the given path.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if none is set.</p>
	 */
	final public static function get(array $array, string $path, string $delimiter = '.', bool $no_throw = false)
	{
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		
		//get
		$pointer = $array;
		foreach (explode($delimiter, $path) as $key) {
			if (!is_array($pointer) || !isset($pointer[$key])) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\PathNotFound([$array, $path]);
			}
			$pointer = $pointer[$key];
		}
		return $pointer;
	}
	
	/**
	 * Set value in a given array at a given path.
	 * 
	 * A path is recognized as <samp>key1 + delimiter + key2 + delimiter + ...</samp>, like so:<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> is equivalent to <code>$array['foo']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar</samp> is equivalent to <code>$array['foo']['bar']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar.123</samp> is equivalent to <code>$array['foo']['bar'][123]</code>.
	 * 
	 * @param array $array [reference]
	 * <p>The array to set in.</p>
	 * @param string $path
	 * <p>The path to set at.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param string $delimiter [default = '.']
	 * <p>The path delimiter character to use.<br>
	 * It must be a single ASCII character.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\PathKeySetIntoNonArray
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the given value was successfully set 
	 * in the given array at the given path, or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function set(
		array &$array, string $path, $value, string $delimiter = '.', bool $no_throw = false
	) {
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		
		//set
		$pointer = &$array;
		foreach (explode($delimiter, $path) as $key) {
			if (isset($pointer) && !is_array($pointer)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\PathKeySetIntoNonArray([$array, $path, $key, $pointer]);
			}
			$pointer = &$pointer[$key];
		}
		$pointer = $value;
		unset($pointer);
		
		//return
		if ($no_throw) {
			return true;
		}
	}
	
	/**
	 * Delete a given path from a given array.
	 * 
	 * A path is recognized as <samp>key1 + delimiter + key2 + delimiter + ...</samp>, like so:<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> is equivalent to <code>$array['foo']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar</samp> is equivalent to <code>$array['foo']['bar']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar.123</samp> is equivalent to <code>$array['foo']['bar'][123]</code>.
	 * 
	 * @param array $array [reference]
	 * <p>The array to delete from.</p>
	 * @param string $path
	 * <p>The path to delete.</p>
	 * @param string $delimiter [default = '.']
	 * <p>The path delimiter character to use.<br>
	 * It must be a single ASCII character.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\PathKeyDeleteFromNonArray
	 * @return void|bool
	 * <p>If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then boolean <code>true</code> is returned if the given path was successfully deleted from the given array,  
	 * or boolean <code>false</code> if otherwise.</p>
	 */
	final public static function delete(array &$array, string $path, string $delimiter = '.', bool $no_throw = false)
	{
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		
		//crumbs
		$crumbs = [];
		$pointer = &$array;
		$keys = explode($delimiter, $path);
		foreach ($keys as $key) {
			if (isset($pointer)) {
				if (!is_array($pointer)) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\PathKeyDeleteFromNonArray([$array, $path, $key, $pointer]);
				} elseif (!array_key_exists($key, $pointer)) {
					break;
				}
			}
			$crumbs[] = ['target' => &$pointer, 'key' => $key];
			$pointer = &$pointer[$key];
		}
		unset($pointer);
		
		//delete
		$delete = count($keys) === count($crumbs);
		foreach (array_reverse($crumbs, true) as &$crumb) {
			if ($delete || empty($crumb['target'][$crumb['key']])) {
				unset($crumb['target'][$crumb['key']]);
			}
			if (!empty($crumb['target'])) {
				break;
			}
		}
		unset($crumb, $crumbs);
		
		//return
		if ($no_throw) {
			return true;
		}
	}
	
	/**
	 * Prepend a given value to a given array.
	 * 
	 * @param array $array [reference]
	 * <p>The array to prepend to.</p>
	 * @param mixed $value
	 * <p>The value to prepend.</p>
	 * @param int|string|null $key [default = null]
	 * <p>The key to prepend with.</p>
	 * @return void
	 */
	final public static function prepend(array &$array, $value, $key = null): void
	{
		if (isset($key)) {
			unset($array[$key]);
			$array = [$key => $value] + $array;
		} else {
			array_unshift($array, $value);
		}
	}
	
	/**
	 * Append a given value to a given array.
	 * 
	 * @param array $array [reference]
	 * <p>The array to append to.</p>
	 * @param mixed $value
	 * <p>The value to append.</p>
	 * @param int|string|null $key [default = null]
	 * <p>The key to append with.</p>
	 * @return void
	 */
	final public static function append(array &$array, $value, $key = null): void
	{
		if (isset($key)) {
			unset($array[$key]);
			$array[$key] = $value;
		} else {
			$array[] = $value;
		}
	}
	
	/**
	 * Get the first value from a given array.
	 * 
	 * @param array $array
	 * <p>The array to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\EmptyArray
	 * @return mixed
	 * <p>The first value from the given array.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if the given array is empty.</p>
	 */
	final public static function first(array $array, bool $no_throw = false)
	{
		if (empty($array)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\EmptyArray();
		}
		return reset($array);
	}
	
	/**
	 * Get the first key from a given array.
	 * 
	 * @param array $array
	 * <p>The array to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\EmptyArray
	 * @return int|string|null
	 * <p>The first key from the given array.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if the given array is empty.</p>
	 */
	final public static function kfirst(array $array, bool $no_throw = false)
	{
		if (empty($array)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\EmptyArray();
		}
		reset($array);
		return key($array);
	}
	
	/**
	 * Get the last value from a given array.
	 * 
	 * @param array $array
	 * <p>The array to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\EmptyArray
	 * @return mixed
	 * <p>The last value from the given array.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if the given array is empty.</p>
	 */
	final public static function last(array $array, bool $no_throw = false)
	{
		if (empty($array)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\EmptyArray();
		}
		return end($array);
	}
	
	/**
	 * Get the last key from a given array.
	 * 
	 * @param array $array
	 * <p>The array to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\EmptyArray
	 * @return int|string|null
	 * <p>The last key from the given array.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if the given array is empty.</p>
	 */
	final public static function klast(array $array, bool $no_throw = false)
	{
		if (empty($array)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\EmptyArray();
		}
		end($array);
		return key($array);
	}
	
	/**
	 * Collapse a given array into a single dimensional pathed one.
	 * 
	 * The returning array is a single dimensional one of non-array values with all the nested keys set as paths.<br>
	 * Each path is set as <samp>key1 + delimiter + key2 + delimiter + ...</samp>, like so:<br>
	 * &nbsp; &#8226; &nbsp; <code>$array['foo']</code> is converted to <samp>foo</samp>;<br>
	 * &nbsp; &#8226; &nbsp; <code>$array['foo']['bar']</code> is converted to <samp>foo.bar</samp>;<br>
	 * &nbsp; &#8226; &nbsp; <code>$array['foo']['bar'][123]</code> is converted to <samp>foo.bar.123</samp>.
	 * 
	 * @param array $array
	 * <p>The array to collapse.</p>
	 * @param string $delimiter [default = '.']
	 * <p>The path delimiter character to use.<br>
	 * It must be a single ASCII character.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the collapse at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::COLLAPSE_ASSOC_EXCLUDE</code> : 
	 * Exclude associative arrays from collapsing.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::COLLAPSE_NONASSOC_EXCLUDE</code> : 
	 * Exclude non-associative arrays from collapsing.</p>
	 * @return array
	 * <p>The collapsed array.</p>
	 */
	final public static function collapse(
		array $array, string $delimiter = '.', ?int $depth = null, int $flags = 0x00
	): array
	{
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//depth
		if ($depth === 0) {
			return $array;
		}
		$next_depth = isset($depth) ? $depth - 1 : null;
		
		//initialize
		$collapse = true;
		$assoc_exclude = (bool)($flags & self::COLLAPSE_ASSOC_EXCLUDE);
		$non_assoc_exclude = (bool)($flags & self::COLLAPSE_NONASSOC_EXCLUDE);
		if ($assoc_exclude && $non_assoc_exclude) {
			return $array;
		} elseif ($assoc_exclude || $non_assoc_exclude) {
			$is_assoc = self::associative($array);
			$collapse = ($assoc_exclude && !$is_assoc) || ($non_assoc_exclude && $is_assoc);
		}
		
		//collapse
		$f_array = [];
		foreach ($array as $k => $v) {
			if (is_array($v) && !empty($v)) {
				//initialize
				$v_collapse = true;
				if ($assoc_exclude || $non_assoc_exclude) {
					$is_v_assoc = self::associative($v);
					$v_collapse = ($assoc_exclude && !$is_v_assoc) || ($non_assoc_exclude && $is_v_assoc);
				}
				
				//collapse
				foreach (self::collapse($v, $delimiter, $next_depth, $flags) as $path => $value) {
					if ($collapse && $v_collapse) {
						$f_array[$k . $delimiter . $path] = $value;
					} else {
						$f_array[$k][$path] = $value;
					}
				}
			} else {
				$f_array[$k] = $v;
			}
		}
		return $f_array;
	}
	
	/**
	 * Expand a given array into a multiple dimensional one.
	 * 
	 * The returning array is a multiple dimensional one with all the paths broken down into nested keys.<br>
	 * Each path is recognized as <samp>key1 + delimiter + key2 + delimiter + ...</samp>, like so:<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> is converted to <code>$array['foo']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar</samp> is converted to <code>$array['foo']['bar']</code>;<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo.bar.123</samp> is converted to <code>$array['foo']['bar'][123]</code>.
	 * 
	 * @param array $array
	 * <p>The array to expand.</p>
	 * @param string $delimiter [default = '.']
	 * <p>The path delimiter character to use.<br>
	 * It must be a single ASCII character.</p>
	 * @param int|null $depth [default = null]
	 * <p>The recursive depth limit to stop the expansion at.<br>
	 * If not set, then no limit is applied, otherwise it must be greater than or equal to <code>0</code>.</p>
	 * @return array
	 * <p>The expanded array.</p>
	 */
	final public static function expand(array $array, string $delimiter = '.', ?int $depth = null): array
	{
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		Call::guardParameter('depth', $depth, !isset($depth) || $depth >= 0, [
			'hint_message' => "Only null or a value greater than or equal to 0 is allowed."
		]);
		
		//depth
		if ($depth === 0) {
			return $array;
		}
		$next_depth = isset($depth) ? $depth - 1 : null;
		
		//expand
		$f_array = [];
		foreach ($array as $path => $value) {
			[$key, $path2] = explode($delimiter, $path, 2) + [1 => null];
			if (isset($path2)) {
				if (!array_key_exists($key, $f_array) || !is_array($f_array[$key])) {
					$f_array[$key] = [];
				}
				$f_array[$key][$path2] = $value;
			} elseif (isset($f_array[$key]) && is_array($f_array[$key]) && is_array($value)) {
				$f_array[$key] += $value;
			} else {
				$f_array[$key] = $value;
			}
		}
		
		//recursion
		foreach ($f_array as $k => $v) {
			if (is_array($v)) {
				$f_array[$k] = self::expand($v, $delimiter, $next_depth);
			}
		}
		
		//return
		return $f_array;
	}
	
	/**
	 * Coalesce value from a given array.
	 * 
	 * The returning value is the first one from the given array which is not <code>null</code>.
	 * 
	 * @param array $array
	 * <p>The array to coalesce from.</p>
	 * @param int[]|string[] $keys [default = []]
	 * <p>The keys to coalesce by.<br>
	 * If empty, then all the values from the given array are used to coalesce by, 
	 * otherwise only the values in the matching keys are used.<br>
	 * The order of these keys also establish the order of the coalesce operation.</p>
	 * @param int|string|null $coalesced_key [reference output] [default = null]
	 * <p>The coalesced key corresponding to the returned value.</p>
	 * @return mixed
	 * <p>The coalesced value from the given array or <code>null</code> if no value is set.</p>
	 */
	final public static function coalesce(array $array, array $keys = [], &$coalesced_key = null)
	{
		$coalesced_key = null;
		foreach (empty($keys) ? array_keys($array) : $keys as $key) {
			if (isset($array[$key])) {
				$coalesced_key = $key;
				return $array[$key];
			}
		}
		return null;
	}
	
	/**
	 * Evaluate a given value as an array.
	 * 
	 * Only the following types and formats can be evaluated into an array:<br>
	 * &nbsp; &#8226; &nbsp; an array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$key, &$value): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.</p>
	 * @param bool $non_associative [default = false]
	 * <p>Do not allow an associative array.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array.</p>
	 * @param bool $recursive [default = false]
	 * <p>Evaluate all possible referenced subobjects into arrays recursively.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an array.</p>
	 */
	final public static function evaluate(
		&$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false,
		bool $recursive = false, bool $nullable = false
	): bool
	{
		return self::processCoercion($value, $evaluator, $non_associative, $non_empty, $recursive, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an array.
	 * 
	 * Only the following types and formats can be coerced into an array:<br>
	 * &nbsp; &#8226; &nbsp; an array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$key, &$value): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.</p>
	 * @param bool $non_associative [default = false]
	 * <p>Do not allow an associative array.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array.</p>
	 * @param bool $recursive [default = false]
	 * <p>Coerce all possible referenced subobjects into arrays recursively.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\CoercionFailed
	 * @return array|null
	 * <p>The given value coerced into an array.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false,
		bool $recursive = false, bool $nullable = false
	): ?array
	{
		self::processCoercion($value, $evaluator, $non_associative, $non_empty, $recursive, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an array.
	 * 
	 * Only the following types and formats can be coerced into an array:<br>
	 * &nbsp; &#8226; &nbsp; an array;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param callable|null $evaluator [default = null]
	 * <p>The evaluator function to use for each element in the resulting array.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (&$key, &$value): bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code><br>
	 * &nbsp; &nbsp; &nbsp; The value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.</p>
	 * @param bool $non_associative [default = false]
	 * <p>Do not allow an associative array.</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty array.</p>
	 * @param bool $recursive [default = false]
	 * <p>Coerce all possible referenced subobjects into arrays recursively.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Data\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an array.</p>
	 */
	final public static function processCoercion(
		&$value, ?callable $evaluator = null, bool $non_associative = false, bool $non_empty = false,
		bool $recursive = false, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//array
		$array = $value;
		if (is_object($array) && $array instanceof IArrayable) {
			$array = $array->toArray($recursive);
		} elseif ($recursive && is_array($array)) {
			foreach ($array as &$v) {
				self::processCoercion($v, $evaluator, $non_associative, $non_empty, $recursive, $nullable, true);
			}
			unset($v);
		}
		
		//coerce
		if (!is_array($array)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only the following types and formats can be coerced into an array:\n" . 
					" - an array;\n" . 
					" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Arrayable\" interface."
			]);
		} elseif ($non_empty && empty($array)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_EMPTY,
				'error_message' => "An empty array is not allowed."
			]);
		} elseif ($non_associative && self::associative($array)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_ASSOCIATIVE,
				'error_message' => "An associative array is not allowed."
			]);
		} elseif (isset($evaluator)) {
			//initialize
			Call::assert('evaluator', $evaluator, function (&$key, &$value): bool {});
			$evaluator = \Closure::fromCallable($evaluator);
			$f_array = [];
			
			//evaluate
			foreach ($array as $k => $v) {
				if ($evaluator($k, $v)) {
					$f_array[$k] = $v;
				} elseif ($no_throw) {
					return false;
				} else {
					throw new Exceptions\CoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_ELEMENT,
						'error_message' => Text::fill(
							"The array element at key {{key}} with value {{value}} is not valid.", [
								'key' => Text::stringify($k, null, ['quote_strings' => true]),
								'value' => Text::stringify($v, null, ['quote_strings' => true])
							]
						)
					]);
				}
			}
			
			//finish
			$array = $f_array;
			unset($f_array);
		}
		
		//finish
		$value = $array;
		return true;
	}
}
