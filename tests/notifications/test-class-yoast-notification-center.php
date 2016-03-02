<?php
/**
 * @package Yoast\Tests\Notifications
 */

/**
 * Class Test_Yoast_Notification_Center
 */
class Test_Yoast_Notification_Center extends WPSEO_UnitTestCase {
	/**
	 * Tests:
	 *  + Set notifications to storage
	 *  Get notifications from storage
	 *  + Clear stored notificatoins
	 *  + register_notifications
	 *  ajax_dismiss_notification
	 *  + is_notification_dismissed
	 *  maybe_dismiss_notification
	 *      -- extensive arguments
	 *  clear_dismissal
	 *  + add_notification
	 *  + get_notification_by_id
	 *  display_notifications
	 *
	 * + Notification display for user is called
	 * Notifier resolve is called
	 * Notifier notification is added
	 */

	/**
	 * Remove notifications on tearDown
	 */
	public function tearDown() {
		parent::tearDown();

		$notification_center = Yoast_Notification_Center::get();
		$notification_center->deactivate_hook();
	}

	/**
	 * Test instance.
	 */
	public function test_construct() {
		$subject = Yoast_Notification_Center::get();

		$this->assertTrue( $subject instanceof Yoast_Notification_Center );
	}

	/**
	 * Registering a notifier
	 */
	public function test_register_notifier() {

		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array() ) )
		                     ->getMock();

		$notifier = $this->getMockBuilder( Yoast_Notifier_Interface::class )->getMock();
		$notifier->method( 'apply' )->will( $this->returnValue( true ) );
		$notifier->method( 'get_notification' )->will( $this->returnValue( $notification ) );

		$subject = Yoast_Notification_Center::get();
		$subject->add_notifier( $notifier );

		$this->assertEquals( array( $notifier ), $subject->get_notifiers() );
	}

	/**
	 * Registering a notifier
	 */
	public function test_register_notifier_twice() {

		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array() ) )
		                     ->getMock();

		$notifier = $this->getMockBuilder( Yoast_Notifier_Interface::class )->getMock();
		$notifier->method( 'apply' )->will( $this->returnValue( true ) );
		$notifier->method( 'get_notification' )->will( $this->returnValue( $notification ) );

		$subject = Yoast_Notification_Center::get();
		$subject->add_notifier( $notifier );
		$subject->add_notifier( $notifier );

		$this->assertEquals( array( $notifier ), $subject->get_notifiers() );
	}

	/**
	 * Clear notification after setting
	 */
	public function test_clear_notifications() {
		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array() ) )
		                     ->getMock();

		$notifier = $this->getMockBuilder( Yoast_Notifier_Interface::class )->getMock();
		$notifier->method( 'apply' )->will( $this->returnValue( true ) );
		$notifier->method( 'get_notification' )->will( $this->returnValue( $notification ) );

		$subject = Yoast_Notification_Center::get();
		$subject->add_notifier( $notifier );

		$subject->deactivate_hook();

		$this->assertEquals( array(), $subject->get_notifiers() );
	}

	/**
	 * Add notification
	 */
	public function test_add_notification() {
		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array() ) )
		                     ->getMock();

		$subject = Yoast_Notification_Center::get();
		$subject->add_notification( $notification );

		$this->assertEquals( array( $notification ), $subject->get_notifications() );
	}

	/**
	 * Add wrong notification
	 */
	public function test_add_notification_twice() {
		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array() ) )
		                     ->getMock();

		$subject = Yoast_Notification_Center::get();
		$subject->add_notification( $notification );
		$subject->add_notification( $notification );

		$notifications = $subject->get_notifications();

		$this->assertEquals( 2, count( $notifications ) );
	}

	/**
	 * Add persistent notification twice
	 *
	 * Only one should be in the list.
	 */
	public function test_add_notification_twice_persistent() {
		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array( 'id' => 'id' ) ) )
		                     ->getMock();

		$notification->method( 'get_id' )->will( $this->returnValue( 'id' ) );

		$subject = Yoast_Notification_Center::get();
		$subject->add_notification( $notification );
		$subject->add_notification( $notification );

		$notifications = $subject->get_notifications();

		$this->assertEquals( 1, count( $notifications ) );
	}

	/**
	 * Test for not set dismissal key.
	 */
	public function test_is_notification_dismissed_non_existent_key() {
		$subject = Yoast_Notification_Center::get();
		$this->assertFalse( $subject->is_notification_dismissed( '' ) );
		$this->assertFalse( $subject->is_notification_dismissed( 'invalid' ) );
	}

	/**
	 * Test dismissed notification
	 */
	public function test_is_notification_dismissed() {
		$notification_dismissal_key = 'notification_dismissal';

		$user_id = $this->factory->user->create();
		wp_set_current_user( $user_id );
		update_user_meta( $user_id, $notification_dismissal_key, '1' );

		$subject = Yoast_Notification_Center::get();
		$this->assertTrue( $subject->is_notification_dismissed( $notification_dismissal_key ) );
	}

	/**
	 * Clearing dismissal after it was set
	 */
	public function test_clear_dismissal() {
		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array( 'id' => 'id' ) ) )
		                     ->getMock();

		$notification->method( 'get_id' )->will( $this->returnValue( 'id' ) );
		$notification->method( 'get_dismissal_key' )->will( $this->returnValue( 'dismissal_key' ) );

		$subject = Yoast_Notification_Center::get();

		$user_id = $this->factory->user->create();
		wp_set_current_user( $user_id );

		update_user_meta( $user_id, $notification->get_dismissal_key(), '1' );

		$this->assertTrue( $subject->is_notification_dismissed( $notification->get_dismissal_key() ) );

		$this->assertTrue( $subject->clear_dismissal( $notification ) );

		$this->assertFalse( $subject->is_notification_dismissed( $notification->get_dismissal_key() ) );
	}

	/**
	 * Clearing dismissal after it was set as string
	 */
	public function test_clear_dismissal_as_string() {
		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( 'notification', array( 'id' => 'id' ) ) )
		                     ->getMock();

		$notification->method( 'get_id' )->will( $this->returnValue( 'id' ) );
		$notification->method( 'get_dismissal_key' )->will( $this->returnValue( 'dismissal_key' ) );

		$subject = Yoast_Notification_Center::get();

		$user_id = $this->factory->user->create();
		wp_set_current_user( $user_id );

		update_user_meta( $user_id, $notification->get_dismissal_key(), '1' );

		$this->assertTrue( $subject->is_notification_dismissed( $notification->get_dismissal_key() ) );

		$this->assertTrue( $subject->clear_dismissal( $notification->get_dismissal_key() ) );

		$this->assertFalse( $subject->is_notification_dismissed( $notification->get_dismissal_key() ) );
	}

	/**
	 * Clear dismissal with empty key
	 */
	public function test_clear_dismissal_empty_key() {
		$subject = Yoast_Notification_Center::get();
		$this->assertFalse( $subject->clear_dismissal( '' ) );
	}

	/**
	 * Saving notifications to storage
	 */
	public function test_update_storage() {
		$message = 'b';
		$options = array( 'id' => 'id ' );

		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( $message, $options ) )
		                     ->getMock();

		$notification->method( 'get_id' )->will( $this->returnValue( 'id' ) );
		$notification->method( 'is_persistent' )->will( $this->returnValue( true ) );
		$notification->method( 'to_array' )->will(
			$this->returnValue(
				array(
					'message' => $message,
					'options' => $options,
				)
			)
		);

		$subject = Yoast_Notification_Center::get();
		$subject->add_notification( $notification );

		$subject->update_storage();

		$stored_notifications = get_option( Yoast_Notification_Center::STORAGE_KEY );
		$test                 = WPSEO_Utils::json_encode( array( $notification->to_array() ) );

		$this->assertEquals( $test, $stored_notifications );
	}

	/**
	 * Not saving non-persistant notifications to storage
	 */
	public function test_update_storage_non_persistent() {
		$message = 'b';
		$options = array();

		$notification = $this->getMockBuilder( Yoast_Notification::class )
		                     ->setConstructorArgs( array( $message, $options ) )
		                     ->getMock();

		$notification->method( 'is_persistent' )->will( $this->returnValue( false ) );
		$notification->method( 'to_array' )->will(
			$this->returnValue(
				array(
					'message' => $message,
					'options' => $options,
				)
			)
		);

		$subject = Yoast_Notification_Center::get();
		$subject->add_notification( $notification );

		$subject->update_storage();

		$stored_notifications = get_option( Yoast_Notification_Center::STORAGE_KEY );

		$this->assertFalse( $stored_notifications );
	}
}
