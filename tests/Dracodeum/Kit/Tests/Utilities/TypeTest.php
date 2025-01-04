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
			$this->assertSame($expected_info->name, $info->name);
			$this->assertSame($expected_info->kind, $info->kind);
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
			[TypeTest_Class::class, true],
			[TypeTest_Interface::class, true],
			[TypeTest_Enum::class, true]
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
			['bool', null, new Info(EInfoKind::GENERIC, ['bool'])],
			['int', null, new Info(EInfoKind::GENERIC, ['int'])],
			['float', null, new Info(EInfoKind::GENERIC, ['float'])],
			['string', null, new Info(EInfoKind::GENERIC, ['string'])],
			['array', null, new Info(EInfoKind::GENERIC, ['array'])],
			['Foo_bar123', null, new Info(EInfoKind::GENERIC, ['Foo_bar123'])],
			['Foo\Bar123', null, new Info(EInfoKind::GENERIC, ['Foo\Bar123'])],
			['Foo.Bar123', null, new Info(EInfoKind::GENERIC, ['Foo.Bar123'])],
			['Foo_bar__123', null, new Info(EInfoKind::GENERIC, ['Foo_bar__123'])],
			['Foo\Bar\_123', null, new Info(EInfoKind::GENERIC, ['Foo\Bar\_123'])],
			['\Foo\Bar\_123', null, new Info(EInfoKind::GENERIC, ['\Foo\Bar\_123'])],
			['Foo.Bar._123', null, new Info(EInfoKind::GENERIC, ['Foo.Bar._123'])],
			['+int', null, new Info(EInfoKind::GENERIC, ['int'], '+')],
			['?*+int', null, new Info(EInfoKind::GENERIC, ['int'], '?*+')],
			['u:int', null, new Info(EInfoKind::GENERIC, ['int'], 'u')],
			['u:?*+int', null, new Info(EInfoKind::GENERIC, ['int'], 'u?*+')],
			['_ A u : ? * + int', null, new Info(EInfoKind::GENERIC, ['int'], '_Au?*+')],
			[
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_:!#$%*+-=?@^`~Foo_bar123', null,
				new Info(
					EInfoKind::GENERIC, ['Foo_bar123'],
					'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_!#$%*+-=?@^`~'
				)
			],
			['int()', null, new Info(EInfoKind::GENERIC, ['int'])],
			['int(123)', null, new Info(EInfoKind::GENERIC, ['int'], '', ['123'])],
			['int(max:123)', null, new Info(EInfoKind::GENERIC, ['int'], '', ['max' => '123'])],
			[
				'int ( min : 0, max : "123" ) ', null, new Info(
					EInfoKind::GENERIC, ['int'], '', ['max' => '123', 'min' => '0']
				)
			],
			[
				'int("\"foo bar\"", min:0,  @Param-Foo  , max:123)', null, new Info(
					EInfoKind::GENERIC, ['int'], '', ['"foo bar"', '@Param-Foo', 'max' => '123', 'min' => '0']
				)
			],
			['u:?*+int(0,123)', null, new Info(EInfoKind::GENERIC, ['int'], 'u?*+', ['0', '123'])],
			[
				'_ Au :? *+  int ( max : 123 , @% , "foo Bar", _s456: f00\, \\\\ b@\)R\")', null, new Info(
					EInfoKind::GENERIC, ['int'], '_Au?*+', ['@%', 'foo Bar', '_s456' => 'f00, \ b@)R"', 'max' => '123']
				)
			],
			['array<string>', null, new Info(EInfoKind::GENERIC, ['array', 'string'])],
			['array<int,string>', null, new Info(EInfoKind::GENERIC, ['array', 'int', 'string'])],
			[
				'array<int,array<Foo<string>,Bar>>', null, new Info(
					EInfoKind::GENERIC, ['array', 'int', 'array<Foo<string>,Bar>']
				)
			],
			['Foo_bar123 < _ , B123, bool > ', null, new Info(EInfoKind::GENERIC, ['Foo_bar123', '_', 'B123', 'bool'])],
			[
				'Foo\Bar <K\Ui12\Obj , _43.n456>', null, new Info(
					EInfoKind::GENERIC, ['Foo\Bar', 'K\Ui12\Obj', '_43.n456']
				)
			],
			[
				'Foo.Bar <K.Ui12.Obj , _43\n456>', null, new Info(
					EInfoKind::GENERIC, ['Foo.Bar', 'K.Ui12.Obj', '_43\n456']
				)
			],
			[
				'Foo.Bar <K.Ui12.Obj<bool> , _43\n456, array< int, string >>', null, new Info(
					EInfoKind::GENERIC, ['Foo.Bar', 'K.Ui12.Obj<bool>', '_43\n456', 'array< int, string >']
				)
			],
			[
				'_ Au :? *+Foo.Bar (s:m) <K.Ui12.Obj(n:@vvv,%,\)\<)<bool> , _43\n456(0,123), ?array< int, string > >',
				null, new Info(
					EInfoKind::GENERIC,
					['Foo.Bar', 'K.Ui12.Obj(n:@vvv,%,\)\<)<bool>', '_43\n456(0,123)', '?array< int, string >'],
					'_Au?*+', ['s' => 'm']
				)
			],
			['int[]', null, new Info(EInfoKind::ARRAY, ['int'])],
			['int[][]', null, new Info(EInfoKind::ARRAY, ['int[]'])],
			['int[123]', null, new Info(EInfoKind::ARRAY, ['int'], '', [123])],
			['int [ 456 ] ', null, new Info(EInfoKind::ARRAY, ['int'], '', [456])],
			['int[123][456]', null, new Info(EInfoKind::ARRAY, ['int[123]'], '', [456])],
			['(int)', false, new Info(EInfoKind::GROUP, ['int'])],
			['(int)', true, new Info(EInfoKind::GENERIC, ['int'])],
			['(int|float|string)', false, new Info(EInfoKind::GROUP, ['int|float|string'])],
			['(int|float|string)', true, new Info(EInfoKind::UNION, ['int', 'float', 'string'])],
			['((int|float|string))', false, new Info(EInfoKind::GROUP, ['(int|float|string)'])],
			['((int|float|string))', true, new Info(EInfoKind::UNION, ['int', 'float', 'string'])],
			['((int|float)|string)', false, new Info(EInfoKind::GROUP, ['(int|float)|string'])],
			['((int|float)|string)', true, new Info(EInfoKind::UNION, ['(int|float)', 'string'])],
			['((int|float))|(string)', null, new Info(EInfoKind::UNION, ['((int|float))', '(string)'])],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)', false, new Info(
					EInfoKind::GROUP, ['Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>']
				)
			],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)', true, new Info(
					EInfoKind::UNION, ['Foo.Bar', '_123\Obj\M(\(\),k:f00) & array<string>']
				)
			],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)[]', null, new Info(
					EInfoKind::ARRAY, ['( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)']
				)
			],
			[
				'( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>) [ 123 ]', null, new Info(
					EInfoKind::ARRAY, ['( Foo.Bar | _123\Obj\M(\(\),k:f00) & array<string>)'], '', [123]
				)
			],
			['int|float|string', null, new Info(EInfoKind::UNION, ['int', 'float', 'string'])],
			['int | float | string | Foo.Bar', null, new Info(EInfoKind::UNION, ['int', 'float', 'string', 'Foo.Bar'])],
			['(Foo\Bar|int)|K\Obj\_123', null, new Info(EInfoKind::UNION, ['(Foo\Bar|int)', 'K\Obj\_123'])],
			['Foo\Bar | (int | K\Obj\_123)', null, new Info(EInfoKind::UNION, ['Foo\Bar', '(int | K\Obj\_123)'])],
			['int&float&string', null, new Info(EInfoKind::INTERSECTION, ['int', 'float', 'string'])],
			[
				'int & float & string & Foo.Bar', null, new Info(
					EInfoKind::INTERSECTION, ['int', 'float', 'string', 'Foo.Bar']
				)
			],
			['(Foo\Bar&int)&K\Obj\_123', null, new Info(EInfoKind::INTERSECTION, ['(Foo\Bar&int)', 'K\Obj\_123'])],
			[
				'Foo\Bar & (int & K\Obj\_123)', null, new Info(
					EInfoKind::INTERSECTION, ['Foo\Bar', '(int & K\Obj\_123)']
				)
			],
			['int&float|string', null, new Info(EInfoKind::UNION, ['int&float', 'string'])],
			['int|float&string', null, new Info(EInfoKind::UNION, ['int', 'float&string'])],
			['int | float & string | Foo.Bar', null, new Info(EInfoKind::UNION, ['int', 'float & string', 'Foo.Bar'])],
			['(Foo\Bar|int)&K\Obj\_123', null, new Info(EInfoKind::INTERSECTION, ['(Foo\Bar|int)', 'K\Obj\_123'])],
			[
				'Foo\Bar & (int | K\Obj\_123)', null, new Info(
					EInfoKind::INTERSECTION, ['Foo\Bar', '(int | K\Obj\_123)']
				)
			],
			['(Foo\Bar|int)&K\Obj\_123[]', null, new Info(EInfoKind::INTERSECTION, ['(Foo\Bar|int)', 'K\Obj\_123[]'])],
			[
				'Foo\Bar & (int | K\Obj\_123)[]', null, new Info(
					EInfoKind::INTERSECTION, ['Foo\Bar', '(int | K\Obj\_123)[]']
				)
			],
			['(Foo\Bar & (int | K\Obj\_123)[])', false, new Info(EInfoKind::GROUP, ['Foo\Bar & (int | K\Obj\_123)[]'])],
			[
				'(Foo\Bar & (int | K\Obj\_123)[])', true, new Info(
					EInfoKind::INTERSECTION, ['Foo\Bar', '(int | K\Obj\_123)[]']
				)
			],
			[
				'(U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
					'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[])',
				false, new Info(
					EInfoKind::GROUP, [
						'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
							'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]'
					]
				)
			],
			[
				'(U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
					'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[])',
				true, new Info(
					EInfoKind::INTERSECTION, [
						'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[]',
						'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]'
					]
				)
			],
			[
				'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[] & ' .
					'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]',
				null, new Info(
					EInfoKind::INTERSECTION, [
						'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[]',
						'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]'
					]
				)
			],
			[
				'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>[]', null, new Info(
					EInfoKind::ARRAY, ['U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>']
				)
			],
			[
				'U:*Foo\Bar<a()<ab:b(F:@)<?c(d:1,e:f2)>>>', null, new Info(
					EInfoKind::GENERIC, ['Foo\Bar', 'a()<ab:b(F:@)<?c(d:1,e:f2)>>'], 'U*'
				)
			],
			['a()<ab:b(F:@)<?c(d:1,e:f2)>>', null, new Info(EInfoKind::GENERIC, ['a', 'ab:b(F:@)<?c(d:1,e:f2)>'])],
			['ab:b(F:@)<?c(d:1,e:f2)>', null, new Info(EInfoKind::GENERIC, ['b', '?c(d:1,e:f2)'], 'ab', ['F' => '@'])],
			['?c(d:1,e:f2)', null, new Info(EInfoKind::GENERIC, ['c'], '?', ['d' => '1', 'e' => 'f2'])],
			[
				'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)[]', null, new Info(
					EInfoKind::ARRAY, ['(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)']
				)
			],
			[
				'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)', false, new Info(
					EInfoKind::GROUP, ['+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>']
				)
			],
			[
				'(+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>)', true, new Info(
					EInfoKind::UNION, ['+int(123)', 'K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>']
				)
			],
			[
				'+int(123) | K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>', null, new Info(
					EInfoKind::UNION, ['+int(123)', 'K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>']
				)
			],
			[
				'K\Obj\_123<f00.b4r|$s|_:_,?f00\b4r(52,x:\"\()>', null, new Info(
					EInfoKind::GENERIC, ['K\Obj\_123', 'f00.b4r|$s|_:_', '?f00\b4r(52,x:\"\()']
				)
			],
			['f00.b4r|$s|_:_', null, new Info(EInfoKind::UNION, ['f00.b4r', '$s', '_:_'])],
			['$s', null, new Info(EInfoKind::GENERIC, ['s'], '$')],
			['_:_', null, new Info(EInfoKind::GENERIC, ['_'], '_')],
			['?f00\b4r(52,x:\"\()', null, new Info(EInfoKind::GENERIC, ['f00\b4r'], '?', ['52', 'x' => '"('])]
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
}



/** Test case dummy class. */
class TypeTest_Class {}



/** Test case dummy interface. */
interface TypeTest_Interface {}



/** Test case dummy enumeration. */
enum TypeTest_Enum {}
