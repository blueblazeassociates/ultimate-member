<?php

class UM_Access {

	function __construct() {
	
		$this->redirect_handler = false;
		$this->allow_access = false;

		add_action('template_redirect',  array(&$this, 'template_redirect'), 1000 );
		
	}
	
	/***
	***	@do actions based on priority
	***/
	function template_redirect() {
	
		do_action('um_access_homepage_per_role');
		
		do_action('um_access_global_settings');
		
		do_action('um_access_post_settings');
		
		if ( $this->redirect_handler && !$this->allow_access )
			exit( wp_redirect( $this->redirect_handler ) );
		
	}
	
	/***
	***	@get meta
	***/
	function get_meta() {
		global $post;
		$post_id = $post->ID;
		$meta = get_post_custom( $post_id );
		foreach ($meta as $k => $v){
			if ( strstr($k, '_um_') ) {
				$k = str_replace('_um_', '', $k);
				$array[$k] = $v[0];
			}
		}
		if ( isset( $array ) )
			return (array)$array;
		else
			return array('');
	}

}