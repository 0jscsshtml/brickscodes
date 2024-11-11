<?php

// The public-facing functionality of the plugin.
class Brickscodes_Public {

	// The ID of this plugin.
	private $plugin_name;

	// The version of this plugin.
	private $version;
	
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}
	
}