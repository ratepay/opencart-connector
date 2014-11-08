<?php
class ControllerPaymentRatepay extends Controller {

    private $error = array();

    public function index() {
        require_once(DIR_APPLICATION . '../ratepay/bootstrap.php');
        $helperVersion = new RatepayHelperVersion;

        $this->load->language('payment/ratepay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('ratepay', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
        }

        $this->_setLang('button_save')
            ->_setLang('button_cancel')
            ->_setLang('heading_title')
            ->_setLang('ratepay_enabled')
            ->_setLang('ratepay_disabled')
            ->_setLang('ratepay_on')
            ->_setLang('ratepay_off')
            ->_setLang('ratepay_allowed')
            ->_setLang('ratepay_forbitten')
            ->_setLang('ratepay_status_text')
            ->_setLang('ratepay_sandbox_text')
            ->_setLang('ratepay_sandbox_help')
            ->_setLang('ratepay_title_text')
            ->_setLang('ratepay_title_help')
            ->_setLang('ratepay_description_text')
            ->_setLang('ratepay_profile_id_text')
            ->_setLang('ratepay_security_code_text')
            ->_setLang('ratepay_order_status_text')
            ->_setLang('ratepay_order_status_help')
            ->_setLang('ratepay_order_status_text')
            ->_setLang('ratepay_order_status_help')
            ->_setLang('ratepay_limits_text')
            ->_setLang('ratepay_limits_help')
            ->_setLang('ratepay_limit_max_text')
            ->_setLang('ratepay_limit_min_text')
            ->_setLang('ratepay_ala_text')
            ->_setLang('ratepay_b2b_text')
            ->_setLang('ratepay_pp_editable_text')
            ->_setLang('ratepay_pp_editable_help')
            ->_setLang('ratepay_pp_basket_text')
            ->_setLang('ratepay_pp_basket_help')
            ->_setLang('ratepay_sort_order_text');

        $this->data['ratepay_version']  = $helperVersion->getRatepayVersion();

        if (isset($this->error['warning'])) {
            $this->data['error_permission'] = $this->error['warning'];
        } else {
            $this->data['error_permission'] = false;
        }

        $this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/ratepay&token=' . $this->session->data['token'];
        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->_getData('ratepay_status', true)
            ->_getData('ratepay_sandbox', true)
            ->_getData('ratepay_title')
            ->_getData('ratepay_description')
            ->_getData('ratepay_profile_id')
            ->_getData('ratepay_security_code')
            ->_getData('ratepay_order_success_id')
            ->_getData('ratepay_limit_max')
            ->_getData('ratepay_limit_min')
            ->_getData('ratepay_ala', true)
            ->_getData('ratepay_b2b', true)
            ->_getData('ratepay_pp_editable', true)
            ->_getData('ratepay_pp_basket', true)
            ->_getData('ratepay_sort_order');

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->template = 'payment/ratepay.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function _setLang($value) {
        $this->data[$value] = $this->language->get($value);

        return $this;
    }

    private function _getData($key, $bool = false) {
        if (isset($this->request->post[$key])) {
            $this->data[$key] = $this->request->post[$key];
        } else {
            $this->data[$key] = $this->config->get($key);
        }

        if ($bool) {
            $this->data[$key] = (bool) $this->data[$key];
        }

        return $this;
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/ratepay')) {
            $this->_setLang('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}