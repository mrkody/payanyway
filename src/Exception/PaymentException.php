<?php

namespace Mrkody\Payanyway\Exception;

/**
 * Class PaymentException
 *
 * @author JhaoDa <jhaoda@gmail.com>
 *
 * @package Idma\Robokassa\Exception
 */
class PaymentException extends \Exception {
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        $message = $message ? $message : 'Unknown payment exception.';

        parent::__construct($message, $code, $previous);
    }
}
