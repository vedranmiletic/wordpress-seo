<?php

/**
 * Class Test_Yoast_Notification
 */
class Test_Yoast_Notification extends WPSEO_UnitTestCase {
	/**
	 * Tests:
	 *  - Set options
	 *  - Verify options
	 *  Apply filter 'wpseo_notification_capabilities'
	 *  Apply filter 'wpseo_notification_capability_check'
	 *  Match capabilities
	 *  display_for_current_user
	 *  is_persistent
	 *  get_dismissal_key
	 *  get_priority
	 */

	/**
	 * No ID is not persistent.
	 */
	public function test_not_persistent() {
		$subject = new Yoast_Notification( 'message', array() );
		$this->assertFalse( $subject->is_persistent() );
	}

	/**
	 * Test defaults.
	 */
	public function test_set_defaults() {
		$subject = new Yoast_Notification( 'message', array() );
		$test    = $subject->to_array();

		$this->assertEquals(
			array(
				'type'             => 'updated',
				'id'               => '',
				'nonce'            => null,
				'priority'         => 0.5,
				'data_json'        => array(),
				'dismissal_key'    => null,
				'capabilities'     => array(),
				'capability_check' => 'all',
				'wpseo_page_only'  => false,
			),
			$test['options']
		);
	}

	/**
	 * Verify invalid options
	 */
	public function test_verify_options() {
		$options = array(
			'priority' => 2,
			'capabilities' => false,
			'capability_check' => 'hoi',
		);

		$subject = new Yoast_Notification( 'message', $options );
		$test = $subject->to_array();

		$this->assertEquals( 1, $subject->get_priority() );
		$this->assertEquals( array(), $test['options']['capabilities'] );
		$this->assertEquals( 'all', $test['options']['capability_check'] );
	}

	/**
	 *
	 */
	public function test_wpseo_notification_capabilities() {
		add_filter( 'wpseo_notification_capabilities', array( $this, 'add_wpseo_notification_capabilities' ) );
	}
}
