<?php

/**
 * Set Parameter for a Request and send them to the Paypage.
 *
 * @category PayIntelligent
 * @package PiRatepay_Paypage_Request
 * @copyright Copyright(c) 2012 PayIntellignet GmbH
 * @license
 * @version 1.0.0
 */
class PiRatepay_Paypage_Request_Curl
extends PiRatepay_Paypage_Request_RequestAbstract
{

    /**
     * Send the request to the paypage using curl.
     *
     * @param array $params
     * @return object Paypage response
     */
    public function _jsonRequest(array $params)
    {
        $curlInit = curl_init(parent::getUrl());
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curlInit, CURLOPT_POST, TRUE);
        curl_setopt($curlInit, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($curlInit);
        return $result;
    }

}
