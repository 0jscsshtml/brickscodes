<?php

/**
 * Fired during plugin deactivation
 */
class Brickscodes_Deactivator {
	public static function deactivate() {
		$options = get_option('brickscodes');
		if ( isset($options['bc_options_page_id']) ) {
			$page_id = $options['bc_options_page_id'];
			wp_delete_post( $page_id, true );
			unset($options['bc_options_page_id']);
			unset($options['bc_plugin_activated']);
			update_option('brickscodes', $options);
		}
	}
}
