<?php
/**
 * Plugin Name: Returnless API
 * Plugin URI: https://www.returnless.com/integraties/woocommerce-retour-plugin
 * Description: Extends WooCommerce Rest API.
 * Author: Returnless 
 * WC tested up to: 6.4.1
 * Requires at least: 5.7
 * Requires PHP: 7.0
 * Version: 1.0.7
 * 
 * Copyright: (c) 2018-2024, Returnless (info@returnless.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   Returnless\Api
 * @author    Returnless
 * @category  Plugin
 * @copyright Copyright (c) 2018-2024, Returnless (info@returnless.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */
defined( 'ABSPATH' ) || exit;

use ReturnlessApi\includes\ReturnlessApi;
use Automattic\WooCommerce\Utilities\FeaturesUtil;

$pluginName = 'woocommerce/woocommerce.php';
$wooCommerceActivatedFlag = in_array($pluginName, apply_filters('active_plugins', get_option('active_plugins')));
if (!class_exists('ReturnlessApi', false) && $wooCommerceActivatedFlag) {

    if (!defined('RETURN_API_PLUGIN_PATH')) {
        define('RETURN_API_PLUGIN_PATH', realpath(dirname(__FILE__)));
    }
    include_once dirname(__FILE__ ) . '/includes/returnless-api-autoloader.php';
    ReturnlessApi::instance();
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( FeaturesUtil::class ) ) {
        FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
} );
