<?php

class WPSEO_Utils_Test extends WPSEO_UnitTestCase {


	/**
	 * @covers WPSEO_Options::grant_access
	 */
	public function test_grant_access() {

		if ( is_multisite() ) {
			// should be true when not running multisite
			$this->assertTrue( WPSEO_Utils::grant_access() );
			return; // stop testing, not multisite
		}

		// admins should return true
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$this->assertTrue( WPSEO_Utils::grant_access() );

		// todo test for superadmins

		// editors should return false
		// $user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		// wp_set_current_user( $user_id );
		// $this->assertTrue( WPSEO_Options::grant_access() );
	}

	/**
	* @covers wpseo_is_apache()
	*/
	public function test_wpseo_is_apache() {
		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.2.22';
		$this->assertTrue( WPSEO_Utils::is_apache() );

		$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.5.11';
		$this->assertFalse( WPSEO_Utils::is_apache() );
	}

	/**
	* @covers test_wpseo_is_nginx()
	*/
	public function test_wpseo_is_nginx() {
		$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.5.11';
		$this->assertTrue( WPSEO_Utils::is_nginx() );

		$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.2.22';
		$this->assertFalse( WPSEO_Utils::is_nginx() );
	}

	/**
	 * @covers WPSEO_Frontend::is_home_posts_page
	 */
	public function test_is_home_posts_page() {

		$this->go_to_home();
		$this->assertTrue( WPSEO_Utils::is_home_posts_page( true ) );

		update_option( 'show_on_front', 'page' );
		$this->assertFalse( WPSEO_Utils::is_home_posts_page( true ) );

		// create and go to post
		update_option( 'show_on_front', 'notapage' );
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );
		$this->assertFalse( WPSEO_Utils::is_home_posts_page( true ) );
	}

	/**
	 * @covers WPSEO_Frontend::is_home_static_page
	 */
	public function test_is_home_static_page_1() {
		// on front page
		$this->go_to_home();
		$this->assertFalse( WPSEO_Utils::is_home_static_page( true ) );
	}

	public function test_is_home_static_page_2() {
		// on front page and show_on_front = page
		update_option( 'show_on_front', 'page' );
		$this->go_to_home();
		$this->assertFalse( WPSEO_Utils::is_home_static_page( true ) );
	}

	public function test_is_home_static_page_3() {
		// create page and set it as front page
		$post_id = $this->factory->post->create( array( 'post_type' => 'page' ) );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $post_id );
		$this->go_to( get_permalink( $post_id ) );

		// on front page, show_on_front = page and on static page
		$this->assertTrue( WPSEO_Utils::is_home_static_page( true ) );
	}

	public function test_is_home_static_page_4() {
		$post_id = $this->factory->post->create( array( 'post_type' => 'page' ) );
		update_option( 'page_on_front', $post_id );

		// go to different post but preserve previous options
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );

		// options set but not on front page, should return false
		$this->assertFalse( WPSEO_Utils::is_home_static_page( true ) );
	}

	/**
	 * @covers WPSEO_Frontend::is_posts_page
	 */
	public function test_is_posts_page_1() {
		// on home with show_on_front != page
		update_option( 'show_on_front', 'something' );
		$this->go_to_home();
		$this->assertFalse( WPSEO_Utils::is_posts_page( true ) );
	}

	public function test_is_posts_page_2() {
		// on home with show_on_front = page
		$post_id = $this->factory->post->create( array( 'post_type' => 'page' ) );
		update_option( 'page_on_front', $post_id );
		update_option( 'show_on_front', 'page' );
		$this->go_to_home();
		$this->assertFalse( WPSEO_Utils::is_posts_page( true ) );
	}

	public function test_is_posts_page_3() {
		$this->go_to_home();
		$this->assertFalse( WPSEO_Utils::is_posts_page( true ) );
	}

	public function test_is_posts_page_4() {
		// go to different post but preserve previous options
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );
		$this->assertFalse( WPSEO_Utils::is_posts_page() );
	}
}