<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

use Cheppers\OtpspClient\Utils;

class Redirect extends RedirectBase
{
    /**
     * @var string
     */
    public $merchantId = '';

    /**
     * @var string
     */
    public $customerEmail = '';

    /**
     * @var string
     */
    public $timeoutUrl = '';

    /**
     * @var string
     */
    public $backrefUrl = '';

    /**
     * @var string
     */
    public $langCode = '';

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Product[]
     */
    public $products = [];

    /**
     * @var ShippingAddress
     */
    public $shippingAddress;

    /**
     * @var BillingAddress
     */
    public $billingAddress;

    protected $hashFields = [
        'MERCHANT',
        'ORDER_REF',
        'ORDER_DATE',
        'ORDER_PNAME[]',
        'ORDER_PCODE[]',
        'ORDER_PINFO[]',
        'ORDER_PRICE[]',
        'ORDER_QTY[]',
        'ORDER_VAT[]',
        'ORDER_SHIPPING',
        'PRICES_CURRENCY',
        'DISCOUNT',
    ];

    protected static $propertyMapping = [
        'merchantId' => 'MERCHANT',
        'customerEmail' => 'BILL_EMAIL',
        'timeoutUrl' => 'TIMEOUT_URL',
        'backrefUrl' => 'BACK_REF',
        'langCode' => 'LANGUAGE',
    ];

    public function __construct()
    {
        $this->order = new Order();
        $this->shippingAddress = new ShippingAddress();
        $this->billingAddress = new BillingAddress();
    }

    public static function __set_state($values)
    {
        $instance = new static();
        foreach (static::$propertyMapping as $internal => $external) {
            if (!array_key_exists($internal, $values) || !property_exists($instance, $external)) {
                continue;
            }

            $instance->{$external} = $values[$internal];
        }

        return $instance;
    }

    protected function isEmpty(): bool
    {
        return $this->merchantId === '';
    }

    public function exportData(): array
    {
        $data = parent::exportData(); // TODO: Change the autogenerated stub
        $data = array_merge($data, $this->order->exportData());
        $data = array_merge($data, $this->shippingAddress->exportData());
        $data = array_merge($data, $this->billingAddress->exportData());
        foreach ($this->products as $product) {
            $data = array_merge($data, $product->exportData());
        }

        return $data;
    }

    protected function getHashValues(): array
    {
        $values = [];

        foreach ($this->exportData() as $items) {
            foreach ($items as $key => $value) {
                if (!in_array($key, $this->hashFields)) {
                    continue;
                }
                $values[] = $value;
            }
        }

        return $values;
    }
}
