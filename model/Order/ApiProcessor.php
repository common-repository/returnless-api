<?php
namespace ReturnlessApi\model\Order;

defined( 'ABSPATH' ) || exit;

class ApiProcessor
{
    private $allowedPlugin = array(
        'custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_alg_wc_full_custom_order_number'),
        'wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_order_number'),
        'woocommerce-sequential-order-numbers/woocommerce-sequential-order-numbers.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_order_number'),
        'woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers-pro.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_order_number_formatted'),
        'yith-woocommerce-sequential-order-number-premium/init.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_ywson_custom_number_order_complete'),
        'woocommerce-jetpack/woocommerce-jetpack.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_wcj_order_number'),
        'booster-plus-for-woocommerce/booster-plus-for-woocommerce.php' =>
            array('getter' => 'getOrderiIdByNumber', 'argument' => '_wcj_order_number'),
    );

    /**
     * @param $orderId
     * @return bool|\WC_Order|\WC_Order_Refund
     */
    public function getOrder($orderId)
    {
        if(is_user_logged_in()) {
            $getter = $this->getStrategy();
            if (!empty($getter)) {
                $orderId = $this->{$getter['getter']}($orderId, $getter['argument']) ?: $orderId;
            }
            $order = wc_get_order($orderId);

            return $order;
        } else {
            echo "You're not authorized to see this page";
        };
    }

    /**
     * @param $number
     * @param $metaKey
     * @return int
     */
    private function getOrderiIdByNumber($number, $metaKey)
    {
        global $wpdb;

        // First query in wp_postmeta
        $postMetaTable = _get_meta_table('post');
        $queryPostMeta = $wpdb->prepare(
            'SELECT post_id FROM ' . $postMetaTable . ' WHERE meta_value = %s AND meta_key = "%s"',
            [$number, $metaKey]
        );
        $orderId = (int)$wpdb->get_var($queryPostMeta);

        // If the order ID is found in wp_postmeta, return it
        if ($orderId) {
            return $orderId;
        }

        // Second query in wp_wc_orders_meta
        $wcOrdersMetaTable = $wpdb->prefix . 'wc_orders_meta'; // Adjust this to your table's name if needed
        $queryWcOrdersMeta = $wpdb->prepare(
            'SELECT order_id FROM ' . $wcOrdersMetaTable . ' WHERE meta_value = %s AND meta_key = "%s"',
            [$number, $metaKey]
        );

        return (int)$wpdb->get_var($queryWcOrdersMeta);
    }

    /**
     * @return array|string[]
     */
    private function getStrategy()
    {
        $getter = array();
        foreach ($this->allowedPlugin as $plugin => $value) {
            $activeFlag = in_array($plugin, apply_filters('active_plugins', get_option('active_plugins')));
            if ($activeFlag) {
                $getter = $value;
            }
        }

        return $getter;
    }
}
