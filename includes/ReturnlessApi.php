<?php

namespace ReturnlessApi\includes;

defined( 'ABSPATH' ) || exit;

use ReturnlessApi\controller\RestApi\Order;

final class ReturnlessApi {

    /**
     * The single instance of the class.
     *
     * @var WooCommerce
     * @since 2.1
     */
    protected static $instance = null;

    /**
     * @var Order
     */
    private $returnLessApiOrder;

    /**
     * @return ReturnlessApi|WooCommerce|null
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 2.1
     */
    public function __clone() {
        wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '2.1' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 2.1
     */
    public function __wakeup() {
        wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '2.1' );
    }

    public function __construct() {
        $this->returnLessApiOrder = new Order();
        $this->init_hooks();
    }

    /**
     * @return void
     */
    private function init_hooks() {
        add_action('rest_api_init', array( $this, 'registerOrderEndpoint' ));
    }

    /**
     * @return void
     */
    public function registerOrderEndpoint() {
        register_rest_route(
            'wc/v3',
            'returnless/order' . '/(?P<id>[a-zA-Z0-9-]+)',
            array(
                'permission_callback' => function () {
                    return false;
                },
                'args' => array(
                    'id' => array(
                        'description' => __('Unique ID for the resource.', 'woocommerce'),
                        'type' => 'string',
                    ),
                ),
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array( $this->returnLessApiOrder, 'getOrderByNumber')
                )
            )
        );
    }
}