<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 4/14/20
 * Time: 17:33
 */

namespace api\models\helper;


use common\models\debug\CloudDebug;
use common\models\pay\PayIndex;

class EPayApiHelper extends PayIndex
{
    const PROVIDER_NAME = 'epay_test';
    private $purse;
    private $client;

    public function __construct($purse)
    {
        parent::__construct();
        $this->purse = $purse;
        $this->initClient();
    }

    private function initClient() {
        $params = $this->params[self::PROVIDER_NAME];
        $baseDir = \Yii::getAlias('@cert');
        $this->client = new ApiClientHelper(array(
            'MERCHANT_CERTIFICATE_ID' => $params['merchant_cert_id'],
            'MERCHANT_NAME'           => $params['merchant_name'],
            'PRIVATE_KEY_FN'          => $baseDir.$params['private_key_fn'],
            'PRIVATE_KEY_PASS'        => $params['merchant_private_key_pass'],
            'PRIVATE_KEY_ENCRYPTED'   => 1,
            'XML_TEMPLATE_FN'         => $baseDir.$params['xml_template_fn'],
            'XML_TEMPLATE_CONFIRM_FN' => $baseDir.$params['xml_template_confirm_fn'],
            'PUBLIC_KEY_FN'           => $baseDir.$params['public_key_fn'],
            'MERCHANT_ID'             => $params['merchant_id'],
        ));
    }

    /**
     * Расшифровка данных от ePay.
     * @param  $xml
     */
    public function decodeData($xml) {
        $this->sendLog(['title' => 'income_xml','content' => $xml,'file' => __FILE__,'line' => __LINE__,'status' => CloudDebug::STATUS_INFO,'zone' => CloudDebug::ZONE_PAY,'type' => CloudDebug::TYPE_EPAY]);
        return $this->client->processResponse($xml);
    }
}