<?php

class WPSEO_Title_Test extends WPSEO_UnitTestCase {

	/**
	 * @var WPSEO_Frontend
	 */
	private static $class_instance;

	public static function setUpBeforeClass() {
		self::$class_instance = WPSEO_Title::get_instance();
	}

	public function tearDown() {
		ob_clean();
		self::$class_instance->reset();
	}

	/**
	 * @covers WPSEO_Title::get_content_title
	 */
	public function test_get_content_title() {

		// create and go to post
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );
	
			// test title according to format
		$expected_title = self::$class_instance->get_title_from_options( 'title-post', get_queried_object() );
		$this->assertEquals( $expected_title, self::$class_instance->get_content_title() );

		// test explicit post title
		$explicit_title = 'WPSEO Post Title %%sitename%%';
		WPSEO_Meta::set_value( 'title', $explicit_title, $post_id );

		$post           = get_post( $post_id );
		$expected_title = wpseo_replace_vars( $explicit_title, $post );
		$this->assertEquals( $expected_title, self::$class_instance->get_content_title() );
	}

	/**
	 * @covers WPSEO_Title::get_taxonomy_title
	 */
	public function test_get_taxonomy_title() {

		// @todo fix for multisite
		if ( is_multisite() ) {
			return;
		}

		// create and go to cat archive
		$category_id = wp_create_category( 'Category Name' );
		$this->go_to( get_category_link( $category_id ) );

		// test title according to format
		$expected_title = self::$class_instance->get_title_from_options( 'title-tax-category', (array) get_queried_object() );
		$this->assertEquals( $expected_title, self::$class_instance->get_taxonomy_title() );

		// @todo add test for an explicit wpseo title format
		// we need an easy way to set taxonomy meta though...
	}

	/**
	 * @covers WPSEO_Title::get_author_title
	 */
	public function test_get_author_title() {

		// create and go to author
		$user_id = $this->factory->user->create( );
		$this->go_to( get_author_posts_url( $user_id ) );

		// test general author title
		$expected_title = self::$class_instance->get_title_from_options( 'title-author-wpseo' );
		$this->assertEquals( $expected_title, self::$class_instance->get_author_title() );

		// add explicit title to author meta
		$explicit_title = 'WPSEO Author Title %%sitename%%';
		add_user_meta( $user_id, 'wpseo_title', $explicit_title );

		// test explicit title
		$expected_title = wpseo_replace_vars( 'WPSEO Author Title %%sitename%%', array() );
		$this->assertEquals( $expected_title, self::$class_instance->get_author_title() );
	}

	/**
	 * @covers WPSEO_Title::get_title_from_options
	 */
	public function test_get_title_from_options() {
		// should return an empty string
		$this->assertEmpty( self::$class_instance->get_title_from_options( '__not-existing-index' ) );
	}

	public function test_get_title_from_options_2() {
		// create and go to post
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );

		$var_source     = get_post( $post_id );
		$expected_title = wpseo_replace_vars( '%%title%% %%sep%% %%sitename%%', $var_source );
		$this->assertEquals( $expected_title, self::$class_instance->get( '' ) );
	}

	public function test_get_title_from_options_3() {
		self::$class_instance->reset();
		$post_id = $this->factory->post->create();
		$this->go_to( get_permalink( $post_id ) );

		$var_source     = get_post( $post_id );

		// test with an option that exists
		$index          = 'title-post';
		$expected_title = wpseo_replace_vars( self::$class_instance->options[ $index ], $var_source );
		$this->assertEquals( $expected_title, self::$class_instance->get( '' ) );
	}

	/**
	 * @covers WPSEO_Title::get_default_title
	 */
	public function test_get_default_title() {
		// TODO
	}

//	/**
//	 * @covers WPSEO_Title::add_paging_to_title
//	 */
//	public function test_add_paging_to_title() {
//		$input = 'Initial title';
//
//		// test without paged query var set
//		$expected = $input;
//		$this->assertEquals( $input, self::$class_instance->add_paging_to_title( '', '', $input ) );
//
//		// test with paged set
//		set_query_var( 'paged', 2 );
//		global $wp_query;
//		$expected = self::$class_instance->add_to_title( '', '', $input, $wp_query->query_vars['paged'] . '/' . $wp_query->max_num_pages );
//		$this->assertEquals( $expected, self::$class_instance->add_paging_to_title( '', '', $input ) );
//	}

//	/**
//	 * @covers WPSEO_Title::add_to_title
//	 */
//	public function test_add_to_title_normal() {
//
//		$title      = 'Title';
//		$sep        = ' >> ';
//		$title_part = 'Title Part';
//
//		$expected = $title . $sep . $title_part;
//		$this->assertEquals( $expected, self::$class_instance->add_to_title( $title_part ) );
//	}

//	/**
//	 * @covers WPSEO_Title::add_to_title
//	 */
//	public function test_add_to_title_right() {
//
//		$title      = 'Title';
//		$sep        = ' >> ';
//		$title_part = 'Title Part';
//
//		$expected = $title_part . $sep . $title;
//		$this->assertEquals( $expected, self::$class_instance->add_to_title( $title_part ) );
//	}

	/**
	 * @covers WPSEO_Title::title
	 */
	public function test_title() {
		// @todo
	}

	/**
	 * @covers WPSEO_Title::wp_title
	 */
	public function force_wp_title() {
		// @todo
	}

}