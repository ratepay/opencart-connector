<?php

class RatepayHelperConnection
{
    private $_PROD_URL = 'http://paypage.dev/api/1.0/rppaypageapi.php';
    //private $prod_url = 'https://paymentpage.ratepay.com/api/1.0/rppaypageapi.php';

    private $_INT_URL = 'http://paypage.dev/api/1.0/rppaypageapi.php';
    //private $int_url = 'https://paymentpage-int.ratepay.com/api/1.0/rppaypageapi.php';

    public function getRatepayGatewayUrl($sandbox = false)
    {
        return ($sandbox) ? $this->_INT_URL : $this->_PROD_URL;
    }
}