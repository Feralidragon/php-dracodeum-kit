<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Truncate as Prototype;
use Dracodeum\Kit\Primitives\Text;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Truncate */
class TruncateTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * @dataProvider provideProcessData_Class
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected value.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testProcess(mixed $value, mixed $expected, array $properties): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData(): array
	{
		return [
			["The quick fox jumps. Over the lazy dog.", "", [0]],
			["The quick fox jumps. Over the lazy dog.", "The quick fo", [12]],
			["The quick fox jumps. Over the lazy dog.", "The quick", [12, 'keep_words' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fo", [12, 'keep_sentences' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps. Over t", [27]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps. Over", [27, 'keep_words' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps.", [27, 'keep_sentences' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps. Ove...", [27, 'ellipsis' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps. Ov[..]",
				[27, 'ellipsis' => true, 'ellipsis_string' => '[..]']],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps. Over \u{2026}",
				[27, 'ellipsis' => true, 'unicode' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps. Over\u{2026}",
				[27, 'ellipsis' => true, 'unicode' => true, 'keep_words' => true]],
			["The quick fox jumps. Over the lazy dog.", "The quick fox jumps.\u{2026}",
				[27, 'ellipsis' => true, 'unicode' => true, 'keep_sentences' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}", [14]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x",
				[13, 'unicode' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps. Over the", [31]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps. Over the l",
				[31, 'unicode' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps. Over the",
				[31, 'unicode' => true, 'keep_words' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps.",
				[31, 'unicode' => true, 'keep_sentences' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps. Over the \u{2026}",
				[31, 'unicode' => true, 'ellipsis' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps. Over the \u{21d4}",
				[31, 'unicode' => true, 'ellipsis' => true, 'ellipsis_string' => "\u{21d4}"]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps. Over the\u{21d4}",
				[31, 'unicode' => true, 'ellipsis' => true, 'ellipsis_string' => "\u{21d4}", 'keep_words' => true]],
			["The quick f\u{2003}x jumps. Over the lazy dog.", "The quick f\u{2003}x jumps.\u{21d4}",
				[31, 'unicode' => true, 'ellipsis' => true, 'ellipsis_string' => "\u{21d4}", 'keep_sentences' => true]]
		];
	}
	
	/**
	 * Provide process data (class).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Class(): array
	{
		$data = $this->provideProcessData();
		foreach ($data as &$d) {
			$d[0] = new TruncateTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
	
	/**
	 * Test `ExplanationProducer` interface.
	 * 
	 * @testdox ExplanationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
	 * 
	 * @return void
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [10])->getExplanation());
	}
}



/** Test case dummy class. */
class TruncateTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
