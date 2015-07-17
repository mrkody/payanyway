<?php namespace Mrkody\Payanyway\Exception;

class InvalidParamException extends PaymentException {
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct('Custom parameters must be an array.', $code, $previous);
    }
}
