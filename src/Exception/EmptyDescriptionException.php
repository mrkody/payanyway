<?php namespace Mrkody\Payanyway\Exception;

class EmptyDescriptionException extends PaymentException {
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct('Invoice description is required and cannot be empty.', $code, $previous);
    }
}
