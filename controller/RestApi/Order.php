<?php

namespace ReturnlessApi\controller\RestApi;

defined( 'ABSPATH' ) || exit;

use ReturnlessApi\model\Order\ApiProcessor;

class Order
{
    /**
     * @var ApiProcessor
     */
    private $apiProcessor;

    /**
     * Constructor ReturnLess Api Order Controller
     */
    public function __construct() {
        $this->apiProcessor = new ApiProcessor();
    }

    /**
     * @param $request
     * @return \WP_REST_Response
     */
    public function getOrderByNumber($request) {
        $order = $this->apiProcessor->getOrder($request->get_param('id'));
        $responseData = new \WC_REST_Orders_V2_Controller();
        if (!empty($order)) {
            $responseData = $responseData->prepare_object_for_response($order, $request);
        }

        return $responseData;
    }
}