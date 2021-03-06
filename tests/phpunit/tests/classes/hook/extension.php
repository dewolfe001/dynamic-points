<?php

/**
 * Test case for WordPoints_Dynamic_Points_Hook_Extension.
 *
 * @package WordPoints_Dynamic_Points\PHPUnit
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Dynamic_Points_Hook_Extension.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Dynamic_Points_Hook_Extension
 */
class WordPoints_Dynamic_Points_Hook_Extension_Test
	extends WordPoints_PHPUnit_TestCase {

	/**
	 * Tests validating the settings when they are valid.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings
	 *
	 * @param array $settings The settings.
	 */
	public function test_validate_settings( $settings ) {

		$this->mock_apps();

		wordpoints_dynamic_points_rounding_methods_init(
			wordpoints_extension( 'dynamic_points' )->get_sub_app( 'rounding_methods' )
		);

		$children = wordpoints_entities()->get_sub_app( 'children' );
		$children->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$children->register(
			'test_entity'
			, 'decimal_number_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Decimal_Number'
		);

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$result = $extension->validate_settings(
			array( 'dynamic_points' => $settings )
			, $validator
			, $event_args
		);

		$this->assertFalse( $validator->had_errors() );
		$this->assertSame( array(), $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertSame( array( 'dynamic_points' => $settings ), $result );
	}

	/**
	 * Data provider for valid settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_valid_settings() {
		return array(
			'int_attr' => array(
				array( 'arg' => array( 'test_entity', 'int_attr' ) ),
			),
			'decimal_number_attr' => array(
				array(
					'arg' => array( 'test_entity', 'decimal_number_attr' ),
					'rounding_method' => 'nearest',
				),
			),
			'int_attr_multiply_by_integer' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'multiply_by' => 5,
				),
			),
			'int_attr_multiply_by_decimal' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'multiply_by' => 5.5,
					'rounding_method' => 'nearest',
				),
			),
			'decimal_number_attr_multiply_by_integer' => array(
				array(
					'arg' => array( 'test_entity', 'decimal_number_attr' ),
					'multiply_by' => 5,
					'rounding_method' => 'nearest',
				),
			),
			'decimal_number_attr_multiply_by_decimal' => array(
				array(
					'arg' => array( 'test_entity', 'decimal_number_attr' ),
					'multiply_by' => 5.5,
					'rounding_method' => 'nearest',
				),
			),
			'minimum' => array(
				array( 'arg' => array( 'test_entity', 'int_attr' ), 'min' => 5 ),
			),
			'maximum' => array(
				array( 'arg' => array( 'test_entity', 'int_attr' ), 'max' => 5 ),
			),
			'minimum_and_maximum' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'min' => 1,
					'max' => 5,
				),
			),
		);
	}

	/**
	 * Tests validating the settings when they are invalid.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_invalid_settings
	 *
	 * @param array  $settings The settings, with one invalid or missing.
	 * @param string $invalid  The slug of the setting that is invalid or missing.
	 */
	public function test_validate_settings_invalid( $settings, $invalid ) {

		$this->mock_apps();

		wordpoints_dynamic_points_rounding_methods_init(
			wordpoints_extension( 'dynamic_points' )->get_sub_app( 'rounding_methods' )
		);

		$children = wordpoints_entities()->get_sub_app( 'children' );
		$children->register(
			'test_entity'
			, 'decimal_number_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Decimal_Number'
		);

		$children->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$children->register(
			'test_entity'
			, 'text_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$result = $extension->validate_settings(
			array( 'dynamic_points' => $settings )
			, $validator
			, $event_args
		);

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 1, $errors );
		$this->assertSame(
			array_merge( array( 'dynamic_points' ), $invalid )
			, $errors[0]['field']
		);

		$this->assertSame( array(), $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		if ( ! is_array( $settings ) || array( 'arg' ) === $invalid ) {

			$this->assertSame( array(), $result );

		} else {

			if ( $invalid ) {
				unset( $settings[ $invalid[0] ] );
			}

			$this->assertSame( array( 'dynamic_points' => $settings ), $result );
		}
	}

	/**
	 * Data provider for invalid settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of invalid settings.
	 */
	public function data_provider_invalid_settings() {
		return array(
			'not_array' => array( 'not_array', array() ),
			'no_arg' => array( array( 'not_arg' => array() ), array( 'arg' ) ),
			'arg_not_array' => array( array( 'arg' => 'not_array' ), array( 'arg' ) ),
			'arg_empty' => array( array( 'arg' => array() ), array( 'arg' ) ),
			'arg_nonexistent' => array( array( 'arg' => array( 'nonexistent' ) ), array( 'arg' ) ),
			'arg_not_attr' => array( array( 'arg' => array( 'test_entity' ) ), array( 'arg' ) ),
			'arg_not_int_attr' => array( array( 'arg' => array( 'test_entity', 'text_attr' ) ), array( 'arg' ) ),
			'multiply_by_not_numeric' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'multiply_by' => 'invalid',
					'rounding_method' => 'nearest',
				),
				array( 'multiply_by' ),
			),
			'multiply_by_zero' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'multiply_by' => 0,
					'rounding_method' => 'nearest',
				),
				array( 'multiply_by' ),
			),
			'rounding_method_required_by_multiply_by_not_set' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'multiply_by' => 5.5,
				),
				array(),
			),
			'rounding_method_required_by_arg_not_set' => array(
				array( 'arg' => array( 'test_entity', 'decimal_number_attr' ) ),
				array(),
			),
			'rounding_method_nonexistent' => array(
				array(
					'arg' => array( 'test_entity', 'decimal_number_attr' ),
					'rounding_method' => 'nonexistent',
				),
				array( 'rounding_method' ),
			),
			'rounding_method_empty' => array(
				array(
					'arg' => array( 'test_entity', 'decimal_number_attr' ),
					'rounding_method' => '',
				),
				array( 'rounding_method' ),
			),
			'minimum' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'min' => 'invalid',
				),
				array( 'min' ),
			),
			'maximum' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'max' => 'invalid',
				),
				array( 'max' ),
			),
			'minimum_more_than_maximum' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'min' => 6,
					'max' => 5,
				),
				array( 'max' ),
			),
			'minimum_equals_maximum' => array(
				array(
					'arg' => array( 'test_entity', 'int_attr' ),
					'min' => 5,
					'max' => 5,
				),
				array( 'max' ),
			),
		);
	}

	/**
	 * Tests that should_hit() returns true.
	 *
	 * @since 1.0.0
	 */
	public function test_should_hit() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertTrue( $extension->should_hit( $fire ) );
	}

	/**
	 * Tests that the script data is correct.
	 *
	 * @since 1.0.0
	 */
	public function test_get_ui_script_data() {

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );

		$script_data = $extension->get_ui_script_data();

		$this->assertInternalType( 'array', $script_data );
		$this->assertArrayHasKey( 'arg_label', $script_data );
		$this->assertArrayHasKey( 'multiply_by_label', $script_data );
		$this->assertArrayHasKey( 'rounding_method_label', $script_data );
		$this->assertArrayHasKey( 'rounding_methods', $script_data );
		$this->assertArrayHasKey( 'min_label', $script_data );
		$this->assertArrayHasKey( 'max_label', $script_data );
	}

	/**
	 * Tests filtering the number of points to award.
	 *
	 * @since 1.0.0
	 */
	public function test_filter_points_to_award() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 5, $extension->filter_points_to_award( 5, $fire ) );
	}

	/**
	 * Tests filtering the number of points to award.
	 *
	 * @since 1.0.0
	 */
	public function test_filter_points_to_award_not_using_extension() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 0, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array( 'arg' => array( 'test_entity', 'int_attr' ) )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 3, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when the arg's value isn't set.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_arg_value_not_set() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array( 'arg' => array( 'test_entity', 'int_attr' ) )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 0, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when the arg value is not a number.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_arg_not_number() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array( 'arg' => array( 'test_entity', 'int_attr' ) )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 'not' ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 0, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when the arg is a decimal number.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_decimal_number() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array(
				'arg' => array( 'test_entity', 'decimal_attr' ),
				'rounding_method' => 'nearest',
			)
		);

		wordpoints_dynamic_points_rounding_methods_init(
			wordpoints_extension( 'dynamic_points' )->get_sub_app( 'rounding_methods' )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'decimal_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Decimal_Number'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3.3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 3, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when the rounding method is invalid.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_invalid_rounding_method() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array(
				'arg' => array( 'test_entity', 'decimal_attr' ),
				'rounding_method' => 'invalid',
			)
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'decimal_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Decimal_Number'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3.3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 0, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when it is multiplied by an integer.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_multiply_by_integer() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array(
				'arg' => array( 'test_entity', 'int_attr' ),
				'multiply_by' => 5,
			)
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 15, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when it is multiplied by a decimal number.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_multiply_by_decimal_number() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array(
				'arg' => array( 'test_entity', 'int_attr' ),
				'multiply_by' => 0.5,
				'rounding_method' => 'nearest',
			)
		);

		wordpoints_dynamic_points_rounding_methods_init(
			wordpoints_extension( 'dynamic_points' )->get_sub_app( 'rounding_methods' )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		// 1.5 rounds up to 2.
		$this->assertSame( 2, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when there is a minimum.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_with_minimum() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array( 'arg' => array( 'test_entity', 'int_attr' ), 'min' => 5 )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 5, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when the arg's value isn't set.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_arg_value_not_set_minimum() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array( 'arg' => array( 'test_entity', 'int_attr' ), 'min' => 5 )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 5, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Tests calculating the points value when there is a maximum.
	 *
	 * @since 1.0.0
	 */
	public function test_calculate_points_value_with_maxmimum() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta(
			'dynamic_points'
			, array( 'arg' => array( 'test_entity', 'int_attr' ), 'max' => 2 )
		);

		wordpoints_entities()->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => 3 ) );

		$extension = new WordPoints_Dynamic_Points_Hook_Extension( 'dynamic_points' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$this->assertSame( 2, $extension->filter_points_to_award( 0, $fire ) );
	}

	/**
	 * Test hitting the target.
	 *
	 * @since 1.0.0
	 */
	public function test_hit() {

		$dynamic_points = 3;

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => $dynamic_points ) );

		$reactor = new WordPoints_Points_Hook_Reactor();

		$user_id = $this->fire_reactor( array(), $entity, $reactor );

		$this->assertSame(
			100 + $dynamic_points
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test hitting the target.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_points_set() {

		$dynamic_points = 3;

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => $dynamic_points ) );

		$reactor = new WordPoints_Points_Hook_Reactor();

		$user_id = $this->fire_reactor( array( 'points' => 10 ), $entity, $reactor );

		$this->assertSame(
			100 + 10
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test hitting the target.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_legacy_reactor() {

		$dynamic_points = 3;

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => $dynamic_points ) );

		$reactor = new WordPoints_Points_Hook_Reactor_Legacy();

		$user_id = $this->fire_reactor( array(), $entity, $reactor );

		$this->assertSame(
			100 + $dynamic_points
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test hitting the target.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_legacy_reactor_points_set() {

		$dynamic_points = 3;

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( array( 'id' => 1, 'test_attr' => $dynamic_points ) );

		$reactor = new WordPoints_Points_Hook_Reactor_Legacy();

		$user_id = $this->fire_reactor( array( 'points' => 10 ), $entity, $reactor );

		$this->assertSame(
			100 + 10
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Fire a reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param array                    $settings The reaction settings.
	 * @param WordPoints_Entity        $entity   An entity to use.
	 * @param WordPoints_Hook_ReactorI $reactor  The reactor object.
	 *
	 * @return int The ID of the user who has been awarded points.
	 */
	protected function fire_reactor( $settings, $entity, $reactor ) {

		$this->mock_apps();

		$event_slug = $this->factory->wordpoints->hook_event->create();
		$reactor_slug = $this->factory->wordpoints->hook_reactor->create(
			array( 'class' => get_class( $reactor ) )
		);

		$hooks = wordpoints_hooks();
		$hooks->get_sub_app( 'extensions' )->register(
			'dynamic_points'
			, 'WordPoints_Dynamic_Points_Hook_Extension'
		);

		$hooks->get_sub_app( 'events' )->get_sub_app( 'args' )->register(
			$event_slug
			, 'user'
			, 'WordPoints_PHPUnit_Mock_Hook_Arg'
		);

		$entities = wordpoints_entities();
		$entities->register( 'user', 'WordPoints_PHPUnit_Mock_Entity' );

		$entities->get_sub_app( 'children' )->register(
			'test_entity'
			, 'int_attr'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr_Integer'
		);

		$defaults = array(
			'event'          => $event_slug,
			'reactor'        => $reactor_slug,
			'target'         => array( 'user' ),
			'points'         => 0,
			'points_type'    => 'points',
			'description'    => 'Testing.',
			'log_text'       => 'Testing.',
			'dynamic_points' => array( 'arg' => array( 'test_entity', 'int_attr' ) ),
		);

		$settings = array_merge( $defaults, $settings );

		$user_id = $this->factory->user->create();

		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $user */
		$user = wordpoints_entities()->get( 'user' );
		$user->set_the_value( $user_id );

		$event_args->add_entity( $user );
		$event_args->add_entity( $entity );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertSame( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor->hit( $fire );

		return $user_id;
	}
}

// EOF
