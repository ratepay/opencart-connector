<?php
require_once(DIR_APPLICATION . '../ratepay/bootstrap.php');

class ControllerPaymentRatepay extends Controller {

    protected function index() {
        $this->load->language('payment/ratepay');
        $this->load->model('checkout/order');

        // Getting RatePAY PayPage token
        $initialisierung = $this->_initialisation();
        if (gettype($initialisierung) == "string") {
            $helperConnection = new RatepayHelperConnection;
            $this->data['continue_to_ratepay'] = $helperConnection->getRatepayPayPageUrl($this->config->get('ratepay_sandbox')) . '/paypage/payment/show/lang/de/token/' . $initialisierung;

            //Definitions
            $this->data['text_ratepay_paymentpage'] = str_replace('%s', '"' . $this->config->get('ratepay_title') . '"', $this->language->get('text_ratepay_paymentpage'));

            $this->data['button_confirm'] = $this->language->get('button_goto_paypage');
            $this->data['button_back'] = $this->language->get('button_back');

        } else {
            $this->data['error_common'] = $this->language->get('error_common');
        }

        if ($this->request->get['route'] != 'checkout/guest_step_3') {
            $this->data['back'] = 'index.php?route=checkout/payment';
        } else {
            $this->data['back'] = 'index.php?route=checkout/guest_step_2';
        }

        $this->data['logo'] = "<img src='admin/view/image/payment/ratepay.png'>";

        $this->id = 'payment';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ratepay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/ratepay.tpl';
        } else {
            $this->template = 'default/template/payment/ratepay_checkout.tpl';
        }

        $this->render();
    }

    public function success() {
        $this->load->language('payment/ratepay');
        $this->load->model('checkout/order');

        $date = date("Y-m-d h:i:s");
        $order_id = $this->request->get['order_id'];
        $token = $this->request->get['token'];
        $payment_method = $this->request->get['payment-method'];
        $descriptor = $this->request->get['descriptor'];
        $trasaction_id = $this->request->get['transaction-id'];

        if ($this->_finalisation($token) === true) {
            $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
            $this->model_checkout_order->update($order_id, $this->config->get('ratepay_order_success_id'), 'Bezahlt mit RatePAY '. $this->language->get($payment_method), false);

            $infoArray = array('payment_method' => $payment_method,
                               'transaction_id' => $trasaction_id,
                               'descriptor' => $descriptor);

            $this->_rpDbUpdate('order', 'payment_method', 'RatePAY ' . $this->language->get($payment_method), 'order_id', $order_id);
            $this->_rpDbUpdate('order', 'payment_code', $payment_method, 'order_id', $order_id);
            $this->_rpDbUpdate('order', 'comment', 'Bezahlt mit RatePAY '. $this->language->get($payment_method), 'order_id', $order_id);
            $this->_rpDbUpdate('order', 'date_modified', $date, 'order_id', $order_id);

            $this->_rpDbInsert('order_history', array('order_id' => $order_id,
                                                      'order_status_id' => $this->config->get('ratepay_order_success_id'),
                                                      'notify' => 0,
                                                      'comment' => json_encode($infoArray),
                                                      'date_added' => $date));

            $this->redirect($this->url->link('checkout/success'));
        } else {
            $this->redirect($this->url->link('payment/ratepay/failure'));
        }
    }

    public function failure() {
        $this->load->language('payment/ratepay');

        if (!$this->config->get('ratepay_sandbox')) {
            $this->session->data['ratepay']['hide'] = true;
        }

        $this->data['button_goto_checkout'] = $this->language->get('button_goto_checkout');
        $this->data['error_failure'] = $this->language->get('error_failure');

        $this->data['button_checkout'] = $this->url->link('checkout/checkout');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ratepay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/ratepay.tpl';
        } else {
            $this->template = 'default/template/payment/ratepay_failure.tpl';
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function _initialisation() {
        $requestObject = $this->_getRequestObject();
        $requestModel = $this->_getRequestModel();

        return $requestObject->initialisation($requestModel);
    }

    private function _finalisation($token) {
        $requestObject = $this->_getRequestObject();
        $requestModel = $this->_getRequestModel();

        return $requestObject->finalisation($requestModel, $token);
    }

    private function _getRequestModel() {
        $this->load->model('checkout/order');
        $this->load->model('account/address');
        $this->load->model('checkout/coupon');
        $this->load->model('checkout/voucher');

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $articles = $this->cart->getProducts();

        $requestModel = new PiRatepay_Paypage_Model_Request();

        $requestModel->setProfileId($this->config->get('ratepay_profile_id'));
        $requestModel->setSecurityCode($this->config->get('ratepay_security_code'));
        $requestModel->setSuccessUrl($this->url->link('payment/ratepay/success&order_id=' . $order['order_id']));
        $requestModel->setFailureUrl($this->url->link('payment/ratepay/failure'));
        $requestModel->setOrderId($order['order_id']);
        $requestModel->setTax(array_sum($this->cart->getTaxes()));

        $basketModel = new PiRatepay_Paypage_Model_Basket($order['currency_code'], $order['total']);
        foreach ($articles as $article) {
            $basketModel->addItem(
                new PiRatepay_Paypage_Model_Item(
                    $article['product_id'],
                    $article['name'],
                    $article['quantity'],
                    $article['price'],
                    $article['total'],
                    $this->_getTaxAmount($this->tax->getRates($article['price'], $article['tax_class_id'])) * $article['quantity']
                )
            );

            if (isset($this->session->data['coupon'])) {
                $coupon = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
                if ($coupon['type'] == 'P') {
                    $discount = ($article['price'] * ($coupon['discount'] / 100)) * -1;
                    $basketModel->addItem(
                        new PiRatepay_Paypage_Model_Item(
                            $article['product_id'] . "_" . $coupon['code'],
                            $coupon['name'] . ": " . $article['name'],
                            $article['quantity'],
                            $discount,
                            (int) $article['quantity'] * $discount,
                            0
                        )
                    );
                }
            }
        }

        if (isset($this->session->data['shipping_method'])) {
            $basketModel->addItem(
                new PiRatepay_Paypage_Model_Item(
                    $this->session->data['shipping_method']['code'],
                    $this->session->data['shipping_method']['title'],
                    1,
                    $this->session->data['shipping_method']['cost'],
                    $this->session->data['shipping_method']['cost'],
                    $this->_getTaxAmount($this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']))
                )
            );
        }

        if (isset($this->session->data['coupon'])) {
            $coupon = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
            if ($coupon['type'] != 'P') {
                $discount = $coupon['discount'] * -1;
                $basketModel->addItem(
                    new PiRatepay_Paypage_Model_Item(
                        $coupon['code'],
                        $coupon['name'],
                        1,
                        $discount,
                        $discount,
                        0
                    )
                );
            }
        }

        if (isset($this->session->data['voucher'])) {
            $voucher = $this->model_checkout_voucher->getVoucher($this->session->data['voucher']);
            $basketModel->addItem(
                new PiRatepay_Paypage_Model_Item(
                    $voucher['code'],
                    'Voucher/Giftcard (from ' . $voucher['from_name'] . ')',
                    1,
                    $voucher['amount'] * -1,
                    $voucher['amount'] * -1,
                    0
                )
            );
        }

        $requestModel->setBasket($basketModel);

        $merchantModel = new PiRatepay_Paypage_Model_Merchant();
        $merchantModel->setName('Dummy');
        $merchantModel->setStreet('Dummy');
        $merchantModel->setZip('12345');
        $merchantModel->setCity('Dummy');
        $merchantModel->setPhone('123456');
        $merchantModel->setEmail('Dummy@dummy.de');
        $merchantModel->setFax('');
        $merchantModel->setFactorbank('Dummy');
        $merchantModel->setBanklocation('Dummy');
        $requestModel->setMerchant($merchantModel);

        $customerObject = new PiRatepay_Paypage_Model_Customer();
        $customerObject->setFirstName($order['firstname']);
        $customerObject->setLastName($order['lastname']);
        $customerObject->setEmail($order['email']);
        $customerObject->setGender('U');
        $customerObject->setPhone($order['telephone']);
        $customerObject->setFax($order['fax']);
        $customerObject->setCompanyName($order['payment_company']);
        $customerObject->setVatId($order['payment_company_id']);
        $customerObject->setNationality(strtoupper($order['payment_iso_code_2']));

        $billingAddress = new PiRatepay_Paypage_Model_Address(
            $order['payment_address_1'],
            '',
            $order['payment_postcode'],
            $order['payment_city'],
            $order['payment_iso_code_2']);
        $customerObject->setBillingAddress($billingAddress);

        if (isset($this->session->data['shipping_address_id']) && ($this->session->data['shipping_address_id'] != $this->session->data['payment_address_id'])) {
            $shippingAddress = new PiRatepay_Paypage_Model_Address(
                $order['payment_address_1'],
                '',
                $order['payment_postcode'],
                $order['payment_city'],
                $order['payment_iso_code_2']);
            $customerObject->setShippingAddress($shippingAddress);
        }

        $requestModel->setCustomer($customerObject);

        $requestModel->setFlags(
            array(
                'edit_customer' => (bool) $this->config->get('ratepay_pp_editable'),
                'disable_items' => !(bool) $this->config->get('ratepay_pp_basket')
            )
        );

        return $requestModel;
    }

    private function _getRequestObject() {
        $helperConnection = new RatepayHelperConnection;
        $this->load->model('checkout/order');
        $this->load->model('account/address');

        $requestObject = new PiRatepay_Paypage_Request_Curl();
        $requestObject->setSandBox((bool) $this->config->get('ratepay_sandbox'));
        $requestObject->setId(
            md5(
                $this->config->get('ratepay_profile_id') .
                $this->config->get('ratepay_security_code') .
                $this->session->data['order_id']
            )
        );
        $requestObject->setSandBoxUrl($helperConnection->getRatepayPayPageApiUrl($this->config->get('ratepay_sandbox')));

        return $requestObject;
    }

    private function _rpDbUpdate($table, $fieldName, $fieldValue, $whereName, $whereValue) {
        $query = "UPDATE `" . DB_PREFIX . $table . "` SET ";
        $query .= $fieldName . " = '" . $fieldValue . "' ";
        $query .= "WHERE ". $whereName . " = '" . $whereValue . "'";
        $this->db->query($query);
    }

    private function _rpDbInsert($table, array $fields) {
        $elements = count($fields);

        $query = "INSERT INTO `" . DB_PREFIX . $table . "` SET ";
        foreach($fields as $field => $value) {
            $query .= $field . " = '" . $value . "'";
            if ($elements > 1) {
                $query .=  ", ";
                $elements--;
            }
        }
        $this->db->query($query);
    }

    private function _getTaxAmount($taxes) {
        $taxAmount = 0;
        foreach ($taxes as $key => $value) {
            $taxAmount += $value['amount'];
        }
        return $taxAmount;
    }

}
?>
