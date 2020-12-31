<?php
/**
 * Plugin name: Enhanced Dependencies
 * Plugin URI: https://github.com/crstauf/enhanced-dependencies
 * Description: Enhancements for WordPress dependencies (ex: server push, inlining, async).
 * Author: Caleb Stauffer
 * Author URI: https://develop.calebstauffer.com
 * Version: 1.0
 * License: GPLv3
 * LicenseURI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

defined( 'WPINC' ) || die();

require_once 'classes/plugin.php';

\Enhanced_Dependencies\Plugin::init( __FILE__ );

?>