<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Type as UType;
use Dracodeum\Kit\Utilities\Type\{
	Info,
	Exceptions
};
use Dracodeum\Kit\Utilities\Type\Info\Enums\Kind as EInfoKind;
use Dracodeum\Kit\Enums\Error\Type as EErrorType;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @covers \Dracodeum\Kit\Utilities\Type */
class TypeTest extends TestCase
{
	//Public methods
	/**
	 * Test `exists` method.
	 * 
	 * NOTE: The test is repeated twice in order to test the internal method memoization.
	 * 
	 * @testdox Type::exists('$name')
	 * @dataProvider provideExistsData
	 * @dataProvider provideExistsData
	 * 
	 * @param string $name
	 * The method `$name` parameter to test with.
	 * 
	 * @param bool $expected
	 * The expected method return value.
	 */
	public function testExists(string $name, bool $expected): void
	{
		$this->assertSame($expected, UType::exists($name));
	}
	
	/**
	 * Test `info` method.
	 * 
	 * @testdox Type::info('$name', $degroup)
	 * @dataProvider provideInfoData
	 * 
	 * @param string $name
	 * The method `$name` parameter to test with.
	 * 
	 * @param bool|null $degroup
	 * The method `$degroup` parameter to test with.
	 * 
	 * @param \Dracodeum\Kit\Utilities\Type\Info $expected_info
	 * The expected method return info instance.
	 */
	public function testInfo(string $name, ?bool $degroup, Info $expected_info): void
	{
		foreach ($degroup !== null ? [$degroup] : [false, true] as $dgrp) {
			//initialize
			$info = UType::info($name, $dgrp);
			
			//assert
			$this->assertInstanceOf(Info::class, $info);
			$this->assertSame($info, UType::info($name, $dgrp));
			$this->assertSame($expected_info->kind, $info->kind);
			$this->assertSame($expected_info->name, $info->name);
			$this->assertSame($expected_info->names, $info->names);
			$this->assertSame($expected_info->flags, $info->flags);
			$this->assertSame($expected_info->parameters, $info->parameters);
		}
	}
	
	/**
	 * Test `info` method expecting `null`, an error instance and an `InvalidName` exception to be thrown.
	 * 
	 * @testdox Type::info('$name') --> null | Error | InvalidName exception
	 * @dataProvider provideInfoData_Null_Error_InvalidNameException
	 * 
	 * @param string $name
	 * The method `$name` parameter to test with.
	 * 
	 * @param string|null $expected_name
	 * The expected name to check against.
	 * 
	 * @param bool|null $degroup
	 * The method `$degroup` parameter to test with.
	 */
	public function testInfo_Null_Error_InvalidNameException(
		string $name, ?string $expected_name = null, ?bool $degroup = null
	): void
	{
		$expected_name ??= $name;
		static $exception_class = Exceptions\Info\InvalidName::class;
		foreach ($degroup !== null ? [$degroup] : [false, true] as $dgrp) {
			//null
			$this->assertNull(UType::info($name, $dgrp, EErrorType::NULL));
			
			//error
			$error = UType::info($name, $dgrp, EErrorType::ERROR);
			$this->assertInstanceOf(Error::class, $error);
			$this->assertSame($exception_class, $error->getName());
			$this->assertInstanceOf(Text::class, $error->getText());
			$this->assertInstanceOf($exception_class, $error->getThrowable());
			$this->assertSame($expected_name, $error->getThrowable()->name);
			
			//exception
			$exception = null;
			try {
				UType::info($name, $dgrp);
			} catch (Exceptions\Info\InvalidName $exception) {
				$this->assertSame($expected_name, $exception->name);
			}
			$this->assertInstanceOf($exception_class, $exception);
		}
	}
	
	/**
	 * Test `normalize` method.
	 * 
	 * NOTE: The test is repeated twice in order to test the internal method memoization.
	 * 
	 * @testdox Type::normalize('$name', $flags)
	 * @dataProvider provideNormalizeData
	 * @dataProvider provideNormalizeData
	 * 
	 * @param string $name
	 * The method `$name` parameter to test with.
	 * 
	 * @param int $flags
	 * The method `$flags` parameter to test with.
	 * 
	 * @param string $expected
	 * The expected method return value.
	 */
	public function testNormalize(string $name, int $flags, string $expected): void
	{
		$this->assertSame($expected, UType::normalize($name, $flags));
	}
	
	/**
	 * Test `covariant` method.
	 * 
	 * NOTE: The test is repeated twice in order to test the internal method memoization.
	 * 
	 * @testdox Type::covariant('$type', '$base_type')
	 * @dataProvider provideCovariantData
	 * @dataProvider provideCovariantData
	 * 
	 * @param string $type
	 * The method `$type` parameter to test with.
	 * 
	 * @param string $base_type
	 * The method `$base_type` parameter to test with.
	 * 
	 * @param bool $expected
	 * The expected method return value.
	 */
	public function testCovariant(string $type, string $base_type, bool $expected): void
	{
		$this->assertSame($expected, UType::covariant($type, $base_type));
	}
	
	/**
	 * Test `contravariant` method.
	 * 
	 * NOTE: The test is repeated twice in order to test the internal method memoization.
	 * 
	 * @testdox Type::contravariant('$type', '$base_type')
	 * @dataProvider provideContravariantData
	 * @dataProvider provideContravariantData
	 * 
	 * @param string $type
	 * The method `$type` parameter to test with.
	 * 
	 * @param string $base_type
	 * The method `$base_type` parameter to test with.
	 * 
	 * @param bool $expected
	 * The expected method return value.
	 */
	public function testContravariant(string $type, string $base_type, bool $expected): void
	{
		$this->assertSame($expected, UType::contravariant($type, $base_type));
	}
	
	
	
	//Public static methods
	/**
	 * Provide `exists` method data.
	 * 
	 * @return array
	 * The provided `exists` method data.
	 */
	public static function provideExistsData(): array
	{
		return [
			['', false],
			['void', false],
			['bool', false],
			['int', false],
			['string', false],
			['array', false],
			['mixed', false],
			['Foo', false],
			[stdClass::class, true],
			[TypeTest_Class1::class, true],
			[TypeTest_Interface1::class, true],
			[TypeTest_Enum1::class, true]
		];
	}
	
	/**
	 * Provide `info` method data.
	 * 
	 * @return array
	 * The provided `info` method data.
	 */
	public static function provideInfoData(): array
	{
		return [
			['bool', null, new Info(EInfoKind::GENERIC, 'bool')],
			['int', null, new Info(EInfoKind::GENERIC, 'int')],
			['float', null, new Info(EInfoKind::GENERIC, 'float')],
			['string', null, new Info(EInfoKind::GENERIC, 'string')],
			['array', null, new Info(EInfoKind::GENERIC, 'array')],
			['Foo_bar123', null, new Info(EInfoKind::GENERIC, 'Foo_bar123')],
			['Foo\Bar123', null, new Info(EInfoKind::GENERIC, 'Foo\Bar123')],
			['Foo.Bar123', null, new Info(EInfoKind::GENERIC, 'Foo.Bar123')],
			['Foo_bar__123', null, new Info(EInfoKind::GENERIC, 'Foo_bar__123')],
			['Foo\Bar\_123', null, new Info(EInfoKind::GENERIC, 'Foo\Bar\_123')],
			['\Foo\Bar\_123', null, new Info(EInfoKind::GENERIC, '\Foo\Bar\_123')],
			['Foo.Bar._123', null, new Info(EInfoKind::GENERIC, 'Foo.Bar._123')],
			['+int', null, new Info(EInfoKind::GENERIC, 'int', flags: '+')],
			['?*+int', null, new Info(EInfoKind::GENERIC, 'int', flags: '?*+')],
			['u:int', null, new Info(EInfoKind::GENERIC, 'int', flags: 'u')],
			['u:?*+int', null, new Info(EInfoKind::GENERIC, 'int', flags: 'u?*+')],
			['uA:?*+Foo\Bar123', null, new Info(EInfoKind::GENERIC, 'Foo\Bar123', flags: 'uA?*+')],
			['uA:?*+Foo.Bar123', null, new Info(EInfoKind::GENERIC, 'Foo.Bar123', flags: 'uA?*+')],
			['uA:?*+Foo_bar__123', null, new Info(EInfoKind::GENERIC, 'Foo_bar__123', flags: 'uA?*+')],
			['uA:?*+Foo\Bar\_123', null, new Info(EInfoKind::GENERIC, 'Foo\Bar\_123', flags: 'uA?*+')],
			['uA:?*+\Foo\Bar\_123', null, new Info(EInfoKind::GENERIC, '\Foo\Bar\_123', flags: 'uA?*+')],
			['uA:?*+Foo.Bar._123', null, new Info(EInfoKind::GENERIC, 'Foo.Bar._123', flags: 'uA?*+')],
			['_ A u : ? * + int', null, new Info(EInfoKind::GENERIC, 'int', flags: '_Au?*+')],
			[
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_:!#$%*+-=?@^`~Foo_bar123', null,
				new Info(
					EInfoKind::GENERIC, 'Foo_bar123',
					flags: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_!#$%*+-=?@^`~'
				)
			],
			['int()', null, new Info(EInfoKind::GENERIC, 'int')],
			['int(123)', null, new Info(EInfoKind::GENERIC, 'int', parameters: ['123'])],
			['int(max:123)', null, new Info(EInfoKind::GENERIC, 'int', parameters: ['max' => '123'])],
			[
				'int ( min : 0, max : "123" ) ', null, new Info(
					EInfoKind::GENERIC, 'int', parameters: ['max' => '123', 'min' => '0']
				)
			],
			[
				'int("\"foo bar\"", min:0,  @Param-Foo  , max:123)', null, new Info(
					EInfoKind::GENERIC, 'int', parameters: ['"foo bar"', '@Param-Foo', 'max' => '123', 'min' => '0']
				)
			],
			['u:?*+int(0,123)', null, new Info(EInfoKind::GENERIC, 'int', flags: 'u?*+', parameters: ['0', '123'])],
			[
				'_ Au :? *+  int ( max : 123 , @% , "foo Bar", _s456: f00\, \\\\ b@\)R\")', null, new Info(
					EInfoKind::GENERIC, 'int', flags: '_Au?*+',
					parameters: ['@%', 'foo Bar', '_s456' => 'f00, \ b@)R"', 'max' => '123']
				)
			],
			['array<string>', null, new Info(EInfoKind::GENERIC, 'array', ['string'])],
			['array<int,string>', null, new Info(EInfoKind::GENERIC, 'array', ['int', 'string'])],
			[
				'array<int,array<Foo<string>,Bar>>', null, new Info(
					EInfoKind::GENERIC, 'array', ['int', 'array<Foo<string>,Bar>']
				)
			],
			['Foo_bar123 < _ , B123, bool > ', null, new Info(EInfoKind::GENERIC, 'Foo_bar123', ['_', 'B123', 'bool'])],
			[
				'Foo\Bar <K\Ui12\Obj , _43.n456>', null, new Info(
					EInfoKind::GENERIC, 'Foo\Bar', ['K\Ui12\Obj', '_43.n456']
				)
			],
			[
				'Foo.Bar <K.Ui12.Obj , _43\n456>', null, new Info(
					EInfoKind::GENERIC, 'Foo.Bar', ['K.Ui12.Obj', '_43\n456']
				)
			],
			[
				'Foo.Bar <K.Ui12.Obj<bool> , _43\n456, array< int, string >>', null, new Info(
					EInfoKind::GENERIC, 'Foo.Bar', ['K.Ui12.Obj<bool>', '_43\n456', 'array< int, string >']
				)
			],
			[
				'_ Au :? *+Foo.Bar (s:m) <K.Ui12.Obj(n:@vvv,%,\)\<)<bool> , _43\n456(0,123), ?array< int, string > >',
				null, new Info(
					EInfoKind::GENERIC,
					'Foo.Bar', ['K.Ui12.Obj(n:@vvv,%,\)\<)<bool>', '_43\n456(0,123)', '?array< int, string >'],
					'_Au?*+', ['s' => 'm']
				)
			],
			['int[]', null, new Info(EInfoKind::ARRAY, 'int')],
			['int[][]', null, new Info(EInfoKind::ARRAY, 'int[]')],
			['int[123]', null, new Info(EInfoKind::ARRAY, 'int', parameters: [123])],
			['int [ 456 ] ', null, new Info(EInfoKind::ARRAY, 'int', parameters: [456])],
			['int[123][456]', null, new Info(EInfoKind::ARRAY, 'int[123]', parameters: [456])],
			['(int)', false, new Info(EInfoKind::GROUP, 'int')],
			['(int)', true, new Info(EInfoKind::GENERIC, 'int')],
			['(int|float|string)', false, new Info(EInfoKind::GROUP, 'int|float|string')],
			['(int|float|string)', true, new Info(EInfoKind::UNION, names: ['int', 'float', 'string'])],
			['((int|float|string))', false, new Info(EInfoKind::GROUP, '(int|float|string)')],
			['((int|float|string))', true, new Info(EInfoKind::UNION, names: ['int', 'float', 'string'])],
			['((int|float)|string)', false, new Info(EInfoKind::GROUP, '(int|float)|string')],
			['((int|float)|string)', true, new Info(EInfoKind::UNION, names: ['(int|float)', 'string'])],
			['((int|float))|(string)', null, new Info(EInfoKind::UNION, names: ['((int|float))', '(string)'])],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)', false, new Info(
					EInfoKind::GROUP, 'Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>'
				)
			],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)', true, new Info(
					EInfoKind::UNION, names: ['Foo.Bar', '_123\Obj\M(\(\),k:f00) & array<string>']
				)
			],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)[]', null, new Info(
					EInfoKind::ARRAY, '( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)'
				)
			],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>) [ 123 ]', null, new Info(
					EInfoKind::ARRAY, '( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)', parameters: [123]
				)
			],
			['int|float|string', null, new Info(EInfoKind::UNION, names: ['int', 'float', 'string'])],
			[
				'int | float | string | Foo.Bar', null, new Info(
					EInfoKind::UNION, names: ['int', 'float', 'string', 'Foo.Bar']
				)
			],
			['(Foo\Bar|int)|K\Obj\_123', null, new Info(EInfoKind::UNION, names: ['(Foo\Bar|int)', 'K\Obj\_123'])],
			[
				'Foo\Bar | (int | K\Obj\_123)', null, new Info(
					EInfoKind::UNION, names: ['Foo\Bar', '(int | K\Obj\_123)']
				)
			],
			['int&float&string', null, new Info(EInfoKind::INTERSECTION, names: ['int', 'float', 'string'])],
			[
				'int & float & string & Foo.Bar', null, new Info(
					EInfoKind::INTERSECTION, names: ['int', 'float', 'string', 'Foo.Bar']
				)
			],
			[
				'(Foo\Bar&int)&K\Obj\_123', null, new Info(
					EInfoKind::INTERSECTION, names: ['(Foo\Bar&int)', 'K\Obj\_123']
				)
			],
			[
				'Foo\Bar & (int & K\Obj\_123)', null, new Info(
					EInfoKind::INTERSECTION, names: ['Foo\Bar', '(int & K\Obj\_123)']
				)
			],
			['int&float|string', null, new Info(EInfoKind::UNION, names: ['int&float', 'string'])],
			['int|float&string', null, new Info(EInfoKind::UNION, names: ['int', 'float&string'])],
			[
				'int | float & string | Foo.Bar', null, new Info(
					EInfoKind::UNION, names: ['int', 'float & string', 'Foo.Bar']
				)
			],
			[
				'(Foo\Bar|int)&K\Obj\_123', null, new Info(
					EInfoKind::INTERSECTION, names: ['(Foo\Bar|int)', 'K\Obj\_123']
				)
			],
			[
				'Foo\Bar & (int | K\Obj\_123)', null, new Info(
					EInfoKind::INTERSECTION, names: ['Foo\Bar', '(int | K\Obj\_123)']
				)
			],
			[
				'(Foo\Bar|int)&K\Obj\_123[]', null, new Info(
					EInfoKind::INTERSECTION, names: ['(Foo\Bar|int)', 'K\Obj\_123[]']
				)
			],
			[
				'Foo\Bar & (int | K\Obj\_123)[]', null, new Info(
					EInfoKind::INTERSECTION, names: ['Foo\Bar', '(int | K\Obj\_123)[]']
				)
			],
			['(Foo\Bar & (int | K\Obj\_123)[])', false, new Info(EInfoKind::GROUP, 'Foo\Bar & (int | K\Obj\_123)[]')],
			[
				'(Foo\Bar & (int | K\Obj\_123)[])', true, new Info(
					EInfoKind::INTERSECTION, names: ['Foo\Bar', '(int | K\Obj\_123)[]']
				)
			],
			[
				'(U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
					'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[])',
				false, new Info(
					EInfoKind::GROUP,
					'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
						'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]'
				)
			],
			[
				'(U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
					'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[])',
				true, new Info(
					EInfoKind::INTERSECTION, names: [
						'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[]',
						'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]'
					]
				)
			],
			[
				'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
					'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]',
				null, new Info(
					EInfoKind::INTERSECTION, names: [
						'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[]',
						'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]'
					]
				)
			],
			[
				'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[]', null, new Info(
					EInfoKind::ARRAY, 'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>'
				)
			],
			[
				'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>', null, new Info(
					EInfoKind::GENERIC, 'Foo\Bar', ['a()<ab:b(F:@)<?c(d:1,e:f2)>>'], 'U*'
				)
			],
			['a()<ab:b(F:@)<?c(d:1,e:f2)>>', null, new Info(EInfoKind::GENERIC, 'a', ['ab:b(F:@)<?c(d:1,e:f2)>'])],
			['ab:b(F:@)<?c(d:1,e:f2)>', null, new Info(EInfoKind::GENERIC, 'b', ['?c(d:1,e:f2)'], 'ab', ['F' => '@'])],
			[
				'?c(d:1,e:f2)', null, new Info(
					EInfoKind::GENERIC, 'c', flags: '?', parameters: ['d' => '1', 'e' => 'f2']
				)
			],
			[
				'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]', null, new Info(
					EInfoKind::ARRAY, '(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)'
				)
			],
			[
				'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)', false, new Info(
					EInfoKind::GROUP, '+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>'
				)
			],
			[
				'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)', true, new Info(
					EInfoKind::UNION, names: ['+int(123)', 'K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>']
				)
			],
			[
				'+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>', null, new Info(
					EInfoKind::UNION, names: ['+int(123)', 'K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>']
				)
			],
			[
				'K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>', null, new Info(
					EInfoKind::GENERIC, 'K\Obj\_123', ['f00.b4r|$s|_:_', '?f00\b4r(52,x:\"\()']
				)
			],
			['f00.b4r|$s|_:_', null, new Info(EInfoKind::UNION, names: ['f00.b4r', '$s', '_:_'])],
			['$s', null, new Info(EInfoKind::GENERIC, 's', flags: '$')],
			['_:_', null, new Info(EInfoKind::GENERIC, '_', flags: '_')],
			[
				'?f00\b4r(52,x:\"\()', null, new Info(
					EInfoKind::GENERIC, 'f00\b4r', flags: '?', parameters: ['52', 'x' => '"(']
				)
			]
		];
	}
	
	/**
	 * Provide `info` method data for `null`, an error instance and an `InvalidName` exception to be thrown.
	 * 
	 * @return array
	 * The provided `info` method data for `null`, an error instance and an `InvalidName` exception to be thrown.
	 */
	public static function provideInfoData_Null_Error_InvalidNameException(): array
	{
		return [
			[''],
			[' ', ''],
			["\t", ''],
			['.'],
			['$'],
			['?'],
			['+'],
			['-'],
			['@'],
			['()'],
			['<>'],
			['[]'],
			['123'],
			['1nt'],
			['i+t'],
			['.int'],
			['int.'],
			[',int'],
			['int,'],
			[':int'],
			['int:'],
			[';int'],
			['int;'],
			['"int'],
			['int"'],
			['\'int'],
			['int\''],
			['/int'],
			['int/'],
			['[int'],
			['int['],
			[']int'],
			['int]'],
			['(int'],
			['int('],
			[')int'],
			['int)'],
			['{int'],
			['int{'],
			['}int'],
			['int}'],
			['<int'],
			['int<'],
			['>int'],
			['int>'],
			['&int'],
			['int&'],
			['|int'],
			['int|'],
			['int?'],
			['"int"'],
			['?:int'],
			['??int'],
			['uu:int'],
			['uAu:int'],
			['int()()'],
			['array<string><int>'],
			['?*.+int'],
			['?*a+int'],
			['?*:+int'],
			['int float'],
			['int,float'],
			['int , float'],
			['int+float'],
			['int + float'],
			['int-float'],
			['int - float'],
			['123_Foo_bar'],
			['Foo.bar.123'],
			['Foo.8ar.f00'],
			['Foo\bar\123'],
			['Foo\\8ar\f00'],
			['Foo\bar.f00'],
			['Foo.bar\f00'],
			['Foo..bar.f00'],
			['Foo.bar..f00'],
			['Foo\\\bar\f00'],
			['Foo\bar\\\f00'],
			['()int'],
			['int(:)'],
			['int(,)'],
			['int(:,)'],
			['int(123'],
			['int("foo)'],
			['int(foo")'],
			['int(123))'],
			['int((123)'],
			['int(123,)'],
			['int(,123)'],
			['int(:123)'],
			['int(?:123)'],
			['int(max::)'],
			['int(max:")'],
			['int(max:))'],
			['int(max:()'],
			['int(4ax:123)'],
			['int((max:123)'],
			['int(max:123))'],
			['int(max:"123)'],
			['int(max:123")'],
			['int(max:12"3)'],
			['int(max:"12"3")'],
			['int(max:"12"3)'],
			['int(123 max:456)'],
			['int(123,max:456,)'],
			['int(,123,max:456)'],
			['int(123,,max:456)'],
			['int(123, ,max:456)'],
			['int(max:123,max:123)'],
			['int(max: 123, 456, max: 789)'],
			['<string>array'],
			['array<>'],
			['array< >'],
			['array<string'],
			['array<string(>'],
			['array<string<>'],
			['array<int,string,>'],
			['array<,int,string>'],
			['array<int,,string>'],
			['array<int, ,string>'],
			['array<string(,int>'],
			['array<string<,int>'],
			['[]int'],
			['int[[]]'],
			['int[foo]'],
			['int[]()'],
			['array[]<string>'],
			['(string'],
			['string)'],
			['((string)'],
			['(string))'],
			['int||float'],
			['int&&float'],
			['int | | float'],
			['int & & float'],
			['int|float|string|'],
			['|int|float|string'],
			['int&float&string&'],
			['&int&float&string'],
			['int|float&string|'],
			['|int&float|string'],
			['(int|float|string|)', 'int|float|string|', true],
			['(int&float&string&)', 'int&float&string&', true]
		];
	}
	
	/**
	 * Provide `normalize` method data.
	 * 
	 * @return array
	 * The provided `normalize` method data.
	 */
	public static function provideNormalizeData(): array
	{
		//data
		$data = [];
		foreach ([0x00, UType::NORMALIZE_SHORT_NAME, UType::NORMALIZE_LEADING_BACKSLASH] as $flags) {
			$data = array_merge($data, [
				['', $flags, 'mixed'],
				[' ', $flags, 'mixed'],
				['void', $flags, 'void'],
				['mixed', $flags, 'mixed'],
				['string', $flags, 'string'],
				[' String ', $flags, 'string'],
				['int[]', $flags, 'int[]'],
				['INT [ ]', $flags, 'int[]'],
				['INT [ 332 ]', $flags, 'int[332]'],
				['String | NULL', $flags, '?string'],
				['NULL | String', $flags, '?string'],
				['(((String | NULL)))', $flags, '?string'],
				['(String | NULL)[]', $flags, '?string[]'],
				['String & NULL', $flags, 'string&null'],
				['NULL & String', $flags, 'null&string'],
				['(((String & NULL)))', $flags, 'string&null'],
				['(String & NULL)[]', $flags, '(string&null)[]'],
				[
					'bZ : ? ! Foo (cD : foobar , abc, \,12\"3 , a1:888 ) < Bar , STRING >', $flags,
					'Zb:!?foo(abc,",12\"3",a1:888,cD:foobar)<bar,string>'
				],
				['void | String [ ] [8 ] | foo <Bar, sTring >', $flags, 'void|string[][8]|foo<bar,string>'],
				['void & String [ ] [8 ] & foo <Bar, sTring >', $flags, 'void&string[][8]&foo<bar,string>'],
				['void & String [ ] [8 ] | foo <Bar, sTring >', $flags, 'void&string[][8]|foo<bar,string>'],
				['((((void) & String [ ]) | foo <Bar, (sTring | A) >))', $flags, '(void&string[])|foo<bar,(string|a)>'],
				[
					'(( bZ : ? ! Foo (cD : foobar , abc, \,12\"3 , a1:888 ) < Bar , STRING > ) & (A | B)) []', $flags,
					'(Zb:!?foo(abc,",12\"3",a1:888,cD:foobar)<bar,string>&(a|b))[]'
				]
			]);
		}
		
		//classes, interfaces and enumerations
		$data = array_merge($data, [
			[stdClass::class, 0x00, 'stdClass'],
			[stdClass::class, UType::NORMALIZE_SHORT_NAME, 'stdClass'],
			[stdClass::class, UType::NORMALIZE_LEADING_BACKSLASH, '\stdClass'],
			[TypeTest_Class1::class, 0x00, TypeTest_Class1::class],
			[TypeTest_Class1::class, UType::NORMALIZE_SHORT_NAME, 'TypeTest_Class1'],
			[TypeTest_Class1::class, UType::NORMALIZE_LEADING_BACKSLASH, '\\' . TypeTest_Class1::class],
			[TypeTest_Interface1::class, 0x00, TypeTest_Interface1::class],
			[TypeTest_Interface1::class, UType::NORMALIZE_SHORT_NAME, 'TypeTest_Interface1'],
			[TypeTest_Interface1::class, UType::NORMALIZE_LEADING_BACKSLASH, '\\' . TypeTest_Interface1::class],
			[TypeTest_Enum1::class, 0x00, TypeTest_Enum1::class],
			[TypeTest_Enum1::class, UType::NORMALIZE_SHORT_NAME, 'TypeTest_Enum1'],
			[TypeTest_Enum1::class, UType::NORMALIZE_LEADING_BACKSLASH, '\\' . TypeTest_Enum1::class],
			[
				'(((String | ? ' . TypeTest_Class1::class . ' < T >) [ 100 ] & ((' . stdClass::class . ') | null ) & ' .
					'(VOID | NULL | ((' . TypeTest_Interface1::class . ')) & ' . TypeTest_Enum1::class . ')))',
				0x00,
				'(string|?' . TypeTest_Class1::class . '<t>)[100]&?stdClass&(void|null|' . TypeTest_Interface1::class .
					'&' . TypeTest_Enum1::class . ')'
			],
			[
				'(((String | ? ' . TypeTest_Class1::class . ' < T >) [ 100 ] & ((' . stdClass::class . ') | null ) & ' .
					'(VOID | NULL | ((' . TypeTest_Interface1::class . ')) & ' . TypeTest_Enum1::class . ')))',
				UType::NORMALIZE_SHORT_NAME,
				'(string|?TypeTest_Class1<t>)[100]&?stdClass&(void|null|TypeTest_Interface1&TypeTest_Enum1)'
			],
			[
				'(((String | ? ' . TypeTest_Class1::class . ' < T >) [ 100 ] & ((' . stdClass::class . ') | null ) & ' .
					'(VOID | NULL | ((' . TypeTest_Interface1::class . ')) & ' . TypeTest_Enum1::class . ')))',
				UType::NORMALIZE_LEADING_BACKSLASH,
				'(string|?\\' . TypeTest_Class1::class . '<t>)[100]&?\stdClass&(void|null|\\' .
					TypeTest_Interface1::class . '&\\' . TypeTest_Enum1::class . ')'
			]
		]);
		
		//return
		return $data;
	}
	
	/**
	 * Provide `covariant` method data.
	 * 
	 * @return array
	 * The provided `covariant` method data.
	 */
	public static function provideCovariantData(): array
	{
		return [
			['void', 'void', true],
			['null', 'null', true],
			['mixed', 'mixed', true],
			['null', 'void', true],
			['void', 'null', false],
			['mixed', 'void', true],
			['void', 'mixed', false],
			['null', 'mixed', true],
			['mixed', 'null', false],
			['bool', 'bool', true],
			['int', 'int', true],
			['float', 'float', true],
			['string', 'string', true],
			['array', 'array', true],
			['object', 'object', true],
			['resource', 'resource', true],
			['bool', 'int', false],
			['int', 'float', false],
			['float', 'string', false],
			['string', 'array', false],
			['array', 'object', false],
			['object', 'resource', false],
			['BOOL', 'Bool', true],
			['Int', 'INT', true],
			['BOOL', 'Int', false],
			['INT', 'Bool', false],
			['null', '?string', true],
			['?string', 'null', false],
			['string', '?string', true],
			['?string', 'string', false],
			['object', '?object', true],
			['?object', 'object', false],
			[stdClass::class, 'string', false],
			['string', stdClass::class, false],
			['?' . stdClass::class, 'string', false],
			['string', '?' . stdClass::class, false],
			[stdClass::class, '?string', false],
			['?string', stdClass::class, false],
			['?' . stdClass::class, '?string', false],
			['?string', '?' . stdClass::class, false],
			[stdClass::class, 'object', true],
			['object', stdClass::class, false],
			[stdClass::class, stdClass::class, true],
			[stdClass::class, '?object', true],
			['?object', stdClass::class, false],
			['?' . stdClass::class, 'object', false],
			['object', '?' . stdClass::class, false],
			['?' . stdClass::class, '?object', true],
			['?object', '?' . stdClass::class, false],
			[stdClass::class, 'mixed', true],
			['mixed', stdClass::class, false],
			['?' . stdClass::class, 'mixed', true],
			['mixed', '?' . stdClass::class, false],
			[TypeTest_Class1::class, 'string', false],
			['string', TypeTest_Class1::class, false],
			['?' . TypeTest_Class1::class, 'string', false],
			['string', '?' . TypeTest_Class1::class, false],
			[TypeTest_Class1::class, '?string', false],
			['?string', TypeTest_Class1::class, false],
			['?' . TypeTest_Class1::class, '?string', false],
			['?string', '?' . TypeTest_Class1::class, false],
			[TypeTest_Class1::class, 'object', true],
			['object', TypeTest_Class1::class, false],
			[TypeTest_Class1::class, TypeTest_Class1::class, true],
			[TypeTest_Class1::class, '?object', true],
			['?object', TypeTest_Class1::class, false],
			['?' . TypeTest_Class1::class, 'object', false],
			['object', '?' . stdClass::class, false],
			['?' . TypeTest_Class1::class, '?object', true],
			['?object', '?' . TypeTest_Class1::class, false],
			[TypeTest_Class1::class, 'mixed', true],
			['mixed', TypeTest_Class1::class, false],
			['?' . TypeTest_Class1::class, 'mixed', true],
			['mixed', '?' . TypeTest_Class1::class, false],
			[TypeTest_Class1::class, TypeTest_Class2::class, false],
			[TypeTest_Class2::class, TypeTest_Class1::class, true],
			[TypeTest_Class3::class, TypeTest_Class1::class, false],
			[TypeTest_Class1::class, '?' . TypeTest_Class2::class, false],
			[TypeTest_Class2::class, '?' . TypeTest_Class1::class, true],
			[TypeTest_Class3::class, '?' . TypeTest_Class1::class, false],
			['?' . TypeTest_Class1::class, TypeTest_Class2::class, false],
			['?' . TypeTest_Class2::class, TypeTest_Class1::class, false],
			['?' . TypeTest_Class3::class, TypeTest_Class1::class, false],
			[ '?' . TypeTest_Class1::class, '?' . TypeTest_Class2::class, false],
			[ '?' . TypeTest_Class2::class, '?' . TypeTest_Class1::class, true],
			[ '?' . TypeTest_Class3::class, '?' . TypeTest_Class1::class, false],
			[TypeTest_Interface1::class, 'string', false],
			['string', TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, 'string', false],
			['string', '?' . TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, '?string', false],
			['?string', TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, '?string', false],
			['?string', '?' . TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, 'object', true],
			['object', TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, TypeTest_Interface1::class, true],
			[TypeTest_Interface1::class, '?object', true],
			['?object', TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, 'object', false],
			['object', '?' . TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, '?object', true],
			['?object', '?' . TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, 'mixed', true],
			['mixed', TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, 'mixed', true],
			['mixed', '?' . TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, TypeTest_Interface2::class, false],
			[TypeTest_Interface2::class, TypeTest_Interface1::class, true],
			[TypeTest_Interface3::class, TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, '?' . TypeTest_Interface2::class, false],
			[TypeTest_Interface2::class, '?' . TypeTest_Interface1::class, true],
			[TypeTest_Interface3::class, '?' . TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, TypeTest_Interface2::class, false],
			['?' . TypeTest_Interface2::class, TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface3::class, TypeTest_Interface1::class, false],
			['?' . TypeTest_Interface1::class, '?' . TypeTest_Interface2::class, false],
			['?' . TypeTest_Interface2::class, '?' . TypeTest_Interface1::class, true],
			['?' . TypeTest_Interface3::class, '?' . TypeTest_Interface1::class, false],
			[TypeTest_Enum1::class, 'string', false],
			['string', TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, 'string', false],
			['string', '?' . TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, '?string', false],
			['?string', TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, '?string', false],
			['?string', '?' . TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, 'object', true],
			['object', TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, TypeTest_Enum1::class, true],
			[TypeTest_Enum1::class, '?object', true],
			['?object', TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, 'object', false],
			['object', '?' . TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, '?object', true],
			['?object', '?' . TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, 'mixed', true],
			['mixed', TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, 'mixed', true],
			['mixed', '?' . TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, TypeTest_Enum2::class, false],
			[TypeTest_Enum2::class, TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, TypeTest_Enum2::class, false],
			[TypeTest_Enum2::class, '?' . TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, '?' . TypeTest_Enum2::class, false],
			['?' . TypeTest_Enum2::class, TypeTest_Enum1::class, false],
			['?' . TypeTest_Enum1::class, '?' . TypeTest_Enum2::class, false],
			['?' . TypeTest_Enum2::class, '?' . TypeTest_Enum1::class, false],
			[TypeTest_Class1::class, TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, TypeTest_Class1::class, false],
			[TypeTest_Class1::class, TypeTest_Interface2::class, false],
			[TypeTest_Interface2::class, TypeTest_Class1::class, false],
			[TypeTest_Class1::class, TypeTest_Interface3::class, false],
			[TypeTest_Interface3::class, TypeTest_Class1::class, false],
			[TypeTest_Class2::class, TypeTest_Interface1::class, true],
			[TypeTest_Interface1::class, TypeTest_Class2::class, false],
			[TypeTest_Class2::class, TypeTest_Interface2::class, true],
			[TypeTest_Interface2::class, TypeTest_Class2::class, false],
			[TypeTest_Class2::class, TypeTest_Interface3::class, false],
			[TypeTest_Interface3::class, TypeTest_Class2::class, false],
			[TypeTest_Class3::class, TypeTest_Interface1::class, true],
			[TypeTest_Interface1::class, TypeTest_Class3::class, false],
			[TypeTest_Class3::class, TypeTest_Interface2::class, false],
			[TypeTest_Interface2::class, TypeTest_Class3::class, false],
			[TypeTest_Class3::class, TypeTest_Interface3::class, false],
			[TypeTest_Interface3::class, TypeTest_Class3::class, false],
			[TypeTest_Enum1::class, TypeTest_Interface1::class, false],
			[TypeTest_Interface1::class, TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, TypeTest_Interface2::class, false],
			[TypeTest_Interface2::class, TypeTest_Enum1::class, false],
			[TypeTest_Enum1::class, TypeTest_Interface3::class, false],
			[TypeTest_Interface3::class, TypeTest_Enum1::class, false],
			[TypeTest_Enum2::class, TypeTest_Interface1::class, true],
			[TypeTest_Interface1::class, TypeTest_Enum2::class, false],
			[TypeTest_Enum2::class, TypeTest_Interface2::class, true],
			[TypeTest_Interface2::class, TypeTest_Enum2::class, false],
			[TypeTest_Enum2::class, TypeTest_Interface3::class, false],
			[TypeTest_Interface3::class, TypeTest_Enum2::class, false],
			['foo<string>', 'array<string>', false],
			['array<string>', 'array', true],
			['array', 'array<string>', false],
			['array<string>', 'array<string>', true],
			['array<string,int>', 'array<string>', true],
			['array<string>', 'array<string,int>', false],
			['array<' . stdClass::class . '>', 'array<' . stdClass::class . '>', true],
			['array<' . stdClass::class . '>', 'array<object>', true],
			['array<object>', 'array<' . stdClass::class . '>', false],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class1::class . '<object,' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class1::class . '<object,' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				false
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface2::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface2::class . ',string>',
				false
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',?string>',
				true
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',?string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Interface2::class . ',string>',
				TypeTest_Class1::class . '<object,' . TypeTest_Interface1::class . ',?string>',
				true
			],
			[
				TypeTest_Class1::class . '<object,' . TypeTest_Interface1::class . ',?string>',
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Interface2::class . ',string>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Interface2::class . ',string,int>',
				TypeTest_Class1::class . '<object,' . TypeTest_Interface1::class . ',?string>',
				true
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Interface2::class . ',string>',
				TypeTest_Class1::class . '<object,' . TypeTest_Interface1::class . ',?string,int>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Class1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Class2::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Class2::class . ',string>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Class3::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Enum1::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				false
			],
			[
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Enum2::class . ',string>',
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				true
			],
			[
				TypeTest_Class1::class . '<' . stdClass::class . ',' . TypeTest_Interface1::class . ',string>',
				TypeTest_Class2::class . '<' . stdClass::class . ',' . TypeTest_Enum2::class . ',string>',
				false
			],
			['string', 'string[]', false],
			['string[]', 'string', false],
			['string[]', 'string[][]', false],
			['string[][]', 'string[]', false],
			['int[]', 'string[]', false],
			['string[]', 'int[]', false],
			['string[]', 'string[]', true],
			['string[8]', 'string[]', true],
			['string[]', 'string[8]', false],
			['string[8]', 'string[8]', true],
			['string[7]', 'string[8]', true],
			['string[8]', 'string[7]', false],
			['string[][]', 'string[][]', true],
			['string[8][]', 'string[][]', true],
			['string[][]', 'string[8][]', false],
			['string[8][8]', 'string[][]', true],
			['string[][]', 'string[8][8]', false],
			['string[5][]', 'string[6][]', true],
			['string[6][]', 'string[5][]', false],
			['string[5][]', 'string[6][8]', false],
			['string[6][8]', 'string[5][]', false],
			['string[7][]', 'string[6][8]', false],
			['string[6][8]', 'string[7][]', true],
			['string[5][7]', 'string[6][8]', true],
			['string[7][7]', 'string[6][8]', false],
			['string[5][9]', 'string[6][8]', false],
			['string[7][9]', 'string[6][8]', false],
			[stdClass::class . '[]', 'object[]', true],
			['object[]', stdClass::class . '[]', false],
			[TypeTest_Class1::class . '[]', TypeTest_Class2::class . '[]', false],
			[TypeTest_Class2::class . '[]', TypeTest_Class1::class . '[]', true],
			[TypeTest_Class3::class . '[]', TypeTest_Class1::class . '[]', false],
			[TypeTest_Class1::class . '[]', TypeTest_Interface1::class . '[]', false],
			[TypeTest_Class2::class . '[]', TypeTest_Interface1::class . '[]', true],
			[TypeTest_Class3::class . '[]', TypeTest_Interface1::class . '[]', true],
			[TypeTest_Interface1::class . '[]', TypeTest_Interface2::class . '[]', false],
			[TypeTest_Interface2::class . '[]', TypeTest_Interface1::class . '[]', true],
			[TypeTest_Interface3::class . '[]', TypeTest_Interface1::class . '[]', false],
			[TypeTest_Enum1::class . '[]', TypeTest_Interface1::class . '[]', false],
			[TypeTest_Enum2::class . '[]', TypeTest_Interface1::class . '[]', true],
			['bool', 'int|string|object', false],
			['int|string|object', 'bool', false],
			['int', 'int|string|object', true],
			['int|string|object', 'int', false],
			['string', 'int|string|object', true],
			['int|string|object', 'string', false],
			['object', 'int|string|object', true],
			['int|string|object', 'object', false],
			[stdClass::class, 'int|string|object', true],
			['int|string|object', stdClass::class, false],
			['?int', 'int|string|object', false],
			['int|string|object', '?int', false],
			['int', 'int|string|object|null', true],
			['int|string|object|null', 'int', false],
			['?int', 'int|string|object|null', true],
			['int|string|object|null', '?int', false],
			['int|string', 'int|string|object', true],
			['int|string|object', 'int|string', false],
			['int|string|null', 'int|string|object', false],
			['int|string|object', 'int|string|null', false],
			['int|string|null', 'int|string|object|null', true],
			['int|string|object|null', 'int|string|null', false],
			['?int|string', 'int|string|object', false],
			['int|string|object', '?int|string', false],
			['?int|string', 'int|string|object|null', true],
			['int|string|object|null', '?int|string', false],
			['int|string|object|null', 'int|string|object|null', true],
			['string[]|null', '?string[]', false],
			['?string[]', 'string[]|null', false],
			['(string|null)[]', '?string[]', true],
			['?string[]', '(string|null)[]', true],
			[
				TypeTest_Class1::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				false
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Class1::class,
				false
			],
			[
				TypeTest_Class2::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				true
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Class2::class,
				false
			],
			[
				TypeTest_Interface1::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				true
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Interface1::class,
				false
			],
			[
				TypeTest_Interface2::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				true
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Interface2::class,
				false
			],
			[
				TypeTest_Class1::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum1::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				false
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Class1::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum1::class,
				false
			],
			[
				TypeTest_Class2::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum1::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				true
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Class2::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum1::class,
				false
			],
			[
				TypeTest_Class3::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum2::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				true
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Class3::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum2::class,
				false
			],
			[
				TypeTest_Class3::class . '|' . TypeTest_Enum1::class,
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				true
			],
			[
				stdClass::class . '|' . TypeTest_Interface1::class . '|' . TypeTest_Enum1::class,
				TypeTest_Class3::class . '|' . TypeTest_Enum1::class,
				false
			],
			[
				TypeTest_Class3::class . '|' . TypeTest_Interface2::class . '|' . TypeTest_Enum2::class,
				stdClass::class . '|' . TypeTest_Enum1::class,
				false
			],
			[TypeTest_Class1::class, TypeTest_Class1::class . '&' . TypeTest_Interface1::class, false],
			[TypeTest_Class1::class . '&' . TypeTest_Interface1::class, TypeTest_Class1::class, true],
			[TypeTest_Class2::class, TypeTest_Class1::class . '&' . TypeTest_Interface1::class, true],
			[TypeTest_Class1::class . '&' . TypeTest_Interface1::class, TypeTest_Class2::class, false],
			[TypeTest_Class3::class, TypeTest_Class1::class . '&' . TypeTest_Interface1::class, false],
			[TypeTest_Class1::class . '&' . TypeTest_Interface1::class, TypeTest_Class3::class, false],
			[TypeTest_Interface1::class, TypeTest_Class1::class . '&' . TypeTest_Interface1::class, false],
			[TypeTest_Class1::class . '&' . TypeTest_Interface1::class, TypeTest_Interface1::class, true],
			[TypeTest_Enum1::class, TypeTest_Enum1::class . '&' . TypeTest_Interface1::class, false],
			[TypeTest_Enum1::class . '&' . TypeTest_Interface1::class, TypeTest_Enum1::class, true],
			[TypeTest_Enum2::class, TypeTest_Enum1::class . '&' . TypeTest_Interface1::class, false],
			[TypeTest_Enum1::class . '&' . TypeTest_Interface1::class, TypeTest_Enum2::class, false],
			[TypeTest_Class1::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, false],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Class1::class, false],
			[TypeTest_Class2::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, true],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Class2::class, false],
			[TypeTest_Class3::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, false],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Class3::class, false],
			[
				TypeTest_Class1::class . '&' . TypeTest_Interface2::class,
				TypeTest_Interface1::class . '&' . TypeTest_Interface2::class,
				true
			],
			[
				TypeTest_Interface1::class . '&' . TypeTest_Interface2::class,
				TypeTest_Class1::class . '&' . TypeTest_Interface2::class,
				false
			],
			[TypeTest_Interface1::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, false],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Interface1::class, true],
			[TypeTest_Interface2::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, true],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Interface2::class, true],
			[TypeTest_Enum1::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, false],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Enum1::class, false],
			[TypeTest_Enum2::class, TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, true],
			[TypeTest_Interface1::class . '&' . TypeTest_Interface2::class, TypeTest_Enum2::class, false],
			[TypeTest_Class1::class, TypeTest_Class1::class . '&' . TypeTest_Interface3::class, false],
			[TypeTest_Class1::class . '&' . TypeTest_Interface3::class, TypeTest_Class1::class, true],
			[
				TypeTest_Class2::class . '&' . TypeTest_Interface2::class . '&' . TypeTest_Interface3::class,
				TypeTest_Class1::class . '&' . TypeTest_Interface1::class,
				true
			],
			[
				TypeTest_Class1::class . '&' . TypeTest_Interface1::class,
				TypeTest_Class2::class . '&' . TypeTest_Interface2::class . '&' . TypeTest_Interface3::class,
				false
			],
			[TypeTest_Class1::class, '(' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null', false],
			['(' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null', TypeTest_Class1::class, false],
			[
				'?' . TypeTest_Class1::class,
				'(' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null',
				false
			],
			[
				'(' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null',
				'?' . TypeTest_Class1::class,
				true
			],
			[
				'?' . TypeTest_Class2::class,
				'(' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null',
				true
			],
			[
				'(' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null',
				'?' . TypeTest_Class2::class,
				false
			],			
			[
				'?' . TypeTest_Class1::class . '[]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null)[]',
				false
			],
			[
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')|null)[]',
				'?' . TypeTest_Class1::class . '[]',
				true
			],
			[
				'((' . TypeTest_Class2::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[4]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				'((' . TypeTest_Class2::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[4]',
				false
			],
			[
				TypeTest_Class2::class . '[][7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				TypeTest_Class2::class . '[100][7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				TypeTest_Class2::class . '[][]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				TypeTest_Class2::class . '[][9]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				TypeTest_Class1::class . '[][7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				'?' . TypeTest_Class2::class . '[][7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				TypeTest_Enum2::class . '[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'?' . TypeTest_Enum2::class . '[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'?' . TypeTest_Enum2::class . '[]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				'?' . TypeTest_Enum2::class . '[9]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			
			[
				'(string|' . TypeTest_Enum2::class . ')[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|' . TypeTest_Enum2::class . '|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|' . TypeTest_Enum2::class . '|null|int)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				'(string|(' . TypeTest_Enum2::class . '&' . TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'((string|(' . TypeTest_Enum2::class . '&' . TypeTest_Interface1::class . ')|null)|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|' . TypeTest_Class2::class . '[100]|(' . TypeTest_Enum2::class . '&' .
					TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|?' . TypeTest_Class2::class . '[100]|(' . TypeTest_Enum2::class . '&' .
					TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				'(string|' . TypeTest_Class2::class . '[100][]|(' . TypeTest_Enum2::class . '&' .
					TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				'(string|(' . TypeTest_Class1::class . '&' . TypeTest_Interface2::class . ')[100]|(' .
					TypeTest_Enum2::class . '&' . TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|(' . TypeTest_Class1::class . '&' . TypeTest_Interface2::class . '&object)[100]|(' .
					TypeTest_Enum2::class . '&' . TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|(' . TypeTest_Class1::class . '&' . TypeTest_Interface2::class . '&' . stdClass::class .
					')[100]|(' . TypeTest_Enum2::class . '&' . TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|((' . TypeTest_Class1::class . '&' . TypeTest_Interface2::class . ')|' .
					TypeTest_Class2::class . ')[100]|(' . TypeTest_Enum2::class . '&' . TypeTest_Interface1::class .
					')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				true
			],
			[
				'(string|((' . TypeTest_Class1::class . '&' . TypeTest_Interface2::class . ')|' .
					TypeTest_Class3::class . ')[100]|(' . TypeTest_Enum2::class . '&' . TypeTest_Interface1::class .
					')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			],
			[
				'(string|((' . TypeTest_Class1::class . '&' . TypeTest_Interface2::class . ')|' .
					TypeTest_Class2::class . '|null)[100]|(' . TypeTest_Enum2::class . '&' .
					TypeTest_Interface1::class . ')|null)[7]',
				'((' . TypeTest_Class1::class . '&' . TypeTest_Interface1::class . ')[]|(' .
					TypeTest_Interface1::class . '&' . TypeTest_Interface2::class . ')|string|null)[8]',
				false
			]
		];
	}
	
	/**
	 * Provide `contravariant` method data.
	 * 
	 * @return array
	 * The provided `contravariant` method data.
	 */
	public static function provideContravariantData(): array
	{
		$data = [];
		foreach (self::provideCovariantData() as $d) {
			$data[] = [$d[1], $d[0], $d[2]];
		}
		return $data;
	}
}



/** Test case dummy interface 1. */
interface TypeTest_Interface1 {}



/** Test case dummy interface 2. */
interface TypeTest_Interface2 extends TypeTest_Interface1 {}



/** Test case dummy interface 3. */
interface TypeTest_Interface3 {}



/** Test case dummy class 1. */
class TypeTest_Class1 {}



/** Test case dummy class 2. */
class TypeTest_Class2 extends TypeTest_Class1 implements TypeTest_Interface2 {}



/** Test case dummy class 3. */
class TypeTest_Class3 implements TypeTest_Interface1 {}



/** Test case dummy enumeration 1. */
enum TypeTest_Enum1 {}



/** Test case dummy enumeration 2. */
enum TypeTest_Enum2 implements TypeTest_Interface2 {}
