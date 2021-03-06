<?php

/**
 * Test case for WordPoints_Dynamic_Points_Rounding_Method_Up.
 *
 * @package WordPoints_Dynamic_Points\PHPUnit
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Dynamic_Points_Rounding_Method_Up.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Dynamic_Points_Rounding_Method_Up
 */
class WordPoints_Dynamic_Points_Rounding_Method_Up_Test
	extends WordPoints_PHPUnit_TestCase {

	/**
	 * Tests that get_title() returns a title for the rounding method.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title() {

		$method = new WordPoints_Dynamic_Points_Rounding_Method_Up( 'test' );

		$title = $method->get_title();
		$this->assertNotEmpty( $title );
		$this->assertInternalType( 'string', $title );
	}

	/**
	 * Tests that round() rounds values as expected.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_values
	 *
	 * @param mixed $value    The value to round.
	 * @param int   $expected The expected result.
	 */
	public function test_round( $value, $expected ) {

		$method = new WordPoints_Dynamic_Points_Rounding_Method_Up( 'test' );

		$this->assertSame( $expected, $method->round( $value ) );
	}

	/**
	 * Data provider for values to round, and the expected result.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function data_provider_values() {
		return array(
			'int' => array( 43, 43 ),
			'float' => array( 4.3, 5 ),
			'float_up' => array( 4.7, 5 ),
			'string_float' => array( '4.3', 5 ),
			'string_float_up' => array( '4.7', 5 ),
		);
	}
}

// EOF
