<?php
class ModelPaymentRatepay extends Model {
  	public function getMethod($address, $total) {
        $this->load->language('payment/ratepay');
        $method_data = array();

        if (isset($this->session->data['ratepay']['hide']) && $this->session->data['ratepay']['hide'] === true) {
            return $method_data;
        }

        if ($address['iso_code_2'] != 'DE' || $this->session->data['currency'] != "EUR") {
            return $method_data;
        }

        if (!(bool) $this->config->get('ratepay_status')) {
            return $method_data;
        }

        if ($this->config->get('ratepay_profile_id') == "" ||
            $this->config->get('ratepay_security_code') == "") {
            return $method_data;
        }

        if ((int) $this->config->get('ratepay_limit_min') > $total ||
            (int) $this->config->get('ratepay_limit_max') < $total) {
            return $method_data;
        }

        if (!(bool) $this->config->get('ratepay_ala') &&
            (isset($this->session->data['shipping_address_id']) && ($this->session->data['shipping_address_id'] != $this->session->data['payment_address_id']))) {
            return $method_data;
        }

        if (!(bool) $this->config->get('ratepay_b2b') &&
            ($address['company'] != "" || $address['company_id'] != "")) {
            return $method_data;
        }

        $method_data = array(
                            'id'         => 'ratepay',
                            'code'         => 'ratepay',
                            'title'      => $this->config->get('ratepay_title') . '<span class="help">' . $this->config->get('ratepay_description') . '</span>',
                            'sort_order' => $this->config->get('ratepay_sort_order')
                            );

        return $method_data;
    }

}
?>