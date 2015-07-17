<?php

namespace Mrkody\Payanyway\Exception;

class InvalidSumException extends PaymentException {
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct('Invoice sum is required and cannot be less or equals zero.', $code, $previous);
    }
}
