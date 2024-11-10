<?php
/**
 * @package       BRICKSCODES
 * @author        Eric Chong
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Brickscodes
 * Plugin URI:    https://github.com/0jscsshtml/brickscodes/
 * Description:   Elevate Your Bricks Builder Experience with this powerful plugin designed to seamlessly integrate with Bricks Builder. Packed with custom elements, editor enhancements, custom conditions, custom dynamic tags, native elements enhancements, etc.
 * Version:       1.0.0
 * Author:        Eric Chong
 * Author URI:    https://github.com/0jscsshtml/brickscodes/
 * Text Domain:   brickscodes
 * Domain Path:   /lang
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Brickscodes. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name
define( 'BRICKSCODES_NAME', 'brickscodes' );

// Currently plugin version.
define( 'BRICKSCODES_VERSION', '1.0.0' );

// Plugin Root File
define( 'BRICKSCODES_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'BRICKSCODES_PLUGIN_BASE', plugin_basename( __FILE__ ) );

// Plugin Folder Path
define( 'BRICKSCODES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin Folder URL
define( 'BRICKSCODES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'BRICKSCODES_ACF_PATH', BRICKSCODES_PLUGIN_DIR . 'includes/acf/advanced-custom-fields/' );
define( 'BRICKSCODES_ACF_URL', BRICKSCODES_PLUGIN_URL . 'includes/acf/advanced-custom-fields/' );

// The code that runs during plugin activation.
function activate_brickscodes() {
	require_once BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-activator.php';
	Brickscodes_Activator::activate();
}

// The code that runs during plugin deactivation.
function deactivate_brickscodes() {
	require_once BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-deactivator.php';
	Brickscodes_Deactivator::deactivate();
}

register_activation_hook( BRICKSCODES_PLUGIN_FILE, 'activate_brickscodes' );
register_deactivation_hook( BRICKSCODES_PLUGIN_FILE, 'deactivate_brickscodes' );

// The code that runs during plugin uninstallation.
function uninstall_brickscodes() {}
register_uninstall_hook(BRICKSCODES_PLUGIN_FILE, 'uninstall_brickscodes');


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require BRICKSCODES_PLUGIN_DIR . 'includes/brickscodes-main.php';

// Begins execution of the plugin.
function run_brickscodes() {

	$plugin = new Brickscodes();
	$plugin->run();

}
run_brickscodes();
