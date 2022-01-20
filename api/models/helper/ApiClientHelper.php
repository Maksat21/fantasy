<?php
/**
 * Created by PhpStorm.
 * User: kassymbekoff
 * Date: 8/5/19
 * Time: 5:18 PM
 */

namespace api\models\helper;


use Epay\Client;
use Epay\Exceptions\Amount;
use Epay\Exceptions\Certificate;
use Epay\Exceptions\Common;
use Epay\Exceptions\Currency;
use Epay\Exceptions\Order;
use Epay\Sign;

class ApiClientHelper extends Client
{

    /**
     * Создаёт подписанный XML-запрос.
     *
     * @param integer $orderId
     * @param integer $currencyCode
     * @param integer $amount
     * @param boolean $base64encode
     * @return string
     * @throws Amount\EmptyAmount
     * @throws Certificate\UnknownError
     * @throws Common\FileNotFound
     * @throws Currency\EmptyId
     * @throws Currency\InvalidId
     * @throws Order\EmptyId
     * @throws Order\NotNumeric
     * @throws Order\NullId
     */
    public function processRequest($orderId, $currencyCode, $amount, $base64encode = true)
    {
        switch (true) {
            case strlen($orderId) < 1:
                throw new Order\EmptyId();
                break;

            case !is_numeric($orderId):
                throw new Order\NotNumeric();
                break;

            case $orderId < 1:
                throw new Order\NullId();
                break;

            case empty($currencyCode):
                throw new Currency\EmptyId();
                break;

            case !array_key_exists($currencyCode, $this->currencyEnum):
                throw new Currency\InvalidId();
                break;

            case $amount == 0:
                throw new Amount\EmptyAmount();
                break;

            case strlen($this->config['PRIVATE_KEY_FN']) == 0:
                throw new Certificate\UnknownError('Path for Private key not found');
                break;

            case strlen($this->config['XML_TEMPLATE_FN']) == 0:
                throw new Certificate\UnknownError('Path for Private key not found');
                break;
        }

        $request = array(
            'MERCHANT_CERTIFICATE_ID' => $this->config['MERCHANT_CERTIFICATE_ID'],
            'MERCHANT_NAME'           => $this->config['MERCHANT_NAME'],
            'ORDER_ID'                => sprintf('%06d', $orderId),
            'CURRENCY'                => $currencyCode,
            'MERCHANT_ID'             => $this->config['MERCHANT_ID'],
            'AMOUNT'                  => $amount,
            'EXP'                     => date('Ymd', strtotime('+1 year'))
        );

        $request = $this->processXml($this->config['XML_TEMPLATE_FN'], $request);
        $sign    = new Sign($this->config);
        $sign->setInvert(true);

        $xml = sprintf(
            '<document>%s<merchant_sign type="RSA" cert_id="%s">%s</merchant_sign></document>',
            $request,
            $this->config['MERCHANT_CERTIFICATE_ID'],
            $sign->sign64($request)
        );

        if ($base64encode) {
            $xml = base64_encode($xml);
        }

        return $xml;
    }
}