# Changes

* Root `Vendor` class removed
  * `Vendor::useAsLibrary()` &#8594; `System::setAsFramework()`
  * `Vendor::isLibrary()` &#8594; `System::isFramework()`
* Renamed `Stringifiable` interface and trait to `Stringable`
  * `non_stringifiable` &#8594; `non_stringable` (property)