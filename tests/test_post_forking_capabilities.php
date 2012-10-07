<?php

class WP_Test_Post_Forking_Capabilities extends WP_UnitTestCase {

    public $core = null;
    
    function __construct() {

		global $wp_roles;
		global $wp_user_roles;
        
        $instance = $this->get_instance();
		$instance->capabilities->add_caps();

		//force caps to refresh, fixes #32
		$wp_user_roles = null;
		$wp_roles = new WP_Roles();

    }

    function &get_core() {
	
		if ( $this->core == null )
			$this->core = &WP_Test_Post_Forking_Core::$instance;
	
		return $this->core;
		
	}

	function get_instance() {
		return $this->get_core()->get_instance();
	}
	
	function create_branch() {
		return $this->get_core()->create_branch();
	}
	
	function create_fork() {
		return $this->get_core()->create_fork();
	}

	function create_post() {
		return $this->get_core()->create_post();
	}
	
	function test_add_caps() {
    	
    	global $wp_roles;
    	$this->assertTrue( array_key_exists( 'edit_forks', $wp_roles->roles['administrator']['capabilities'] ) );
    	
	}

    function test_fork_post() {
       $post = get_post( $this->create_post() );
       $instance = $this->get_instance();
       $instance->action_init();
       $this->assertTrue( user_can( $post->post_author, 'fork_post', $post->ID ) );        
    }
    
    function test_branch_post() {
        $post = get_post( $this->create_post() );
		$instance = $this->get_instance();
		$instance->action_init();
		$other_user = $this->get_core()->create_user( 'author' );
        $this->assertTrue( user_can( $post->post_author, 'branch_post', $post->ID ) );
        $this->assertFalse( user_can( $other_user, 'branch_post', $post->ID ) );
        
    }
    
    function test_publish_fork() {
        $post = get_post( $this->create_post() );
		$instance = $this->get_instance();
		$instance->action_init();
		$other_user = $this->get_core()->create_user( 'author' );
		$this->assertFalse( user_can( $other_user, 'publish_fork', $post->ID ) );
    }
    
    

}