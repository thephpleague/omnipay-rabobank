<?php

class Store_rabobank_ext
{
    public $name = 'Store Rabobank Payments';
    public $version = '0.0.1';
    public $description = 'Rabobank payment gateway for Expresso Store';
    public $settings_exist = 'n';
    public $docs_url = 'https://exp-resso.com/docs';

    public function activate_extension()
    {
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'store_payment_gateways',
            'hook'      => 'store_payment_gateways',
            'priority'  => 10,
            'settings'  => '',
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);
    }

    /**
     * This hook is called when Store is searching for available payment gateways
     * We will use it to tell Store about our custom gateway
     */
    public function store_payment_gateways($gateways)
    {
        // tell Store about our new payment gateway
        // (this must match the name of your gateway in the Omnipay directory)
        $gateways[] = 'Rabobank';

        // tell PHP where to find the gateway classes
        // Store will automatically include your files when they are needed
        $composer = require(PATH_THIRD.'store/autoload.php');
        $composer->add('Omnipay\Rabobank', __DIR__.'/src');

        return $gateways;
    }
}
