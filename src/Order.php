<?php

namespace Omnipay\Rabobank;

class Order
{
    /**
     * OrderId as delivered during the PurchaseRequest
     *
     * @var string
     */
    public $merchantOrderId;

    /**
     * The unique id that the omnikassa has assigned to this order
     *
     * @var string
     */
    public $omnikassaOrderId;

    /**
     * Unique identification of the webshop (point of interaction), seen from ROK.
     * This is relevant if several webshops use the same webhook URL.
     *
     * @var int
     */
    public $poiId;

    /**
     * The status of the order.
     *
     * @var string  'COMPLETED'|'EXPIRED'|'IN_PROGRESS'|'CANCELLED'
     */
    public $orderStatus;

    /**
     * The moment this status is reached
     *
     * @var string  ISO 8601
     */
    public $orderStatusDateTime;

    /**
     * Future field, for now: always empty
     *
     * @var string|null
     */
    public $errorCode;

    /**
     * Array with keys
     *      - currency  The currency in which payment is made
     *      - amount    The amount paid by the consumer, in cents
     *
     * @var array
     */
    public $paidAmount;

    /**
     * Array with keys
     *      - currency  The currency of the order
     *      - amount    The total amount of the order, in cents
     *
     * @var array
     */
    public $totalAmount;
}
