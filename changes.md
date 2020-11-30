# Changes

* Root `Vendor` class removed
  * `Vendor::useAsLibrary()` &#8594; `System::setAsFramework()`
  * `Vendor::isLibrary()` &#8594; `System::isFramework()`
* Renamed `Stringifiable` interface and trait to `Stringable`
  * `non_stringifiable` &#8594; `non_stringable` (property)
* Removed `$recursive` parameter from most methods
  * `Arrayable`
    * `toArray(bool $recursive = false)` &#8594; `toArray()`
  * `Cloneable`
    * `clone(bool $recursive = false)` &#8594; `clone()`
      * `Type::clone(object $object, bool $recursive = false)` &#8594; `Type::clone(object $object)`
  * `DebugInfo`
    * `getDebugInfo(bool $recursive = false)` &#8594; `getDebugInfo()`