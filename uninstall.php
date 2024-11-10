<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$all_users = get_users( array( 'fields' => 'ID' ) );
foreach ( $all_users as $user_id ) {
	delete_user_meta( intval($user_id), 'bc_user_current_login' );
	delete_user_meta( intval($user_id), 'bc_user_last_login' );
}
	
$cache_key_core = 'cached_' . md5('brickscodes' . '_bc_core_integration');
delete_transient($cache_key_core);	
$cache_key = 'cached_' . md5('brickscodes' . '_bc_query_manager');
delete_transient($cache_key);
delete_option('brickscodes');
