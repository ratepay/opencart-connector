<?php

class RatepayHelperConnection
{
    private $_PROD_URL = 'https://paymentpage.ratepay.com';

    private $_INT_URL = 'https://paymentpage-int.ratepay.com';

    private $_URL_SUFFIX = '/api/1.0/rppaypageapi.php';

    public function getRatepayPayPageApiUrl($sandbox = false)
    {
        return ($sandbox) ? $this->_INT_URL . $this->_URL_SUFFIX : $this->_PROD_URL . $this->_URL_SUFFIX;
    }

    public function getRatepayPayPageUrl($sandbox = false)
    {
        return ($sandbox) ? $this->_INT_URL : $this->_PROD_URL;
    }
}