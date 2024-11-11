<?php

/**

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class Brickscodes_i18n {

	public function bc_load_plugin_textdomain() {

		load_plugin_textdomain(
			'brickscodes',
			false,
			dirname( dirname( BRICKSCODES_PLUGIN_BASE ) ) . '/lang/'
		);

	}

}