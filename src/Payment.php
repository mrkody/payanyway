<?php

/**
 * This file is part of Robokassa package.
 *
 * (c) 2014 IDM Agency (http://idma.ru)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mrkody\Payanyway;

use Idma\Robokassa\Exception\InvalidSumException;
use Idma\Robokassa\Exception\InvalidParamException;
use Idma\Robokassa\Exception\InvalidInvoiceIdException;
use Idma\Robokassa\Exception\EmptyDescriptionException;

/**
 * Class Payment
 *
 * @author JhaoDa <jhaoda@gmail.com>
 *
 * @package Mrkody\Payanyway
 */
class Payment {
    private $baseUrl      = 'https://www.moneta.ru/assistantWizard.htm';
    private $isTestMode   = false;
    private $valid        = false;
    private $data;
    private $customParams = [];
    private $submit_button_class;
    private $submit_button_name;

    private $mnt_id;
    private $mnt_currency_code;
    private $moneta_locale;
    private $paymentPassword;

    /**
     * Class constructor.
     *
     * @param  string $login              login of Merchant
     * @param  string $paymentPassword    password #1
     * @param  string $validationPassword password #2
     * @param  bool   $testMode           use test server
     */
    public function __construct($mnt_id, $mnt_currency_code, $moneta_locale, $paymentPassword, $testMode = false, $submit_button_class = '', $submit_button_name = 'Pay')
    {
        $this->mnt_id              = $mnt_id;
        $this->mnt_currency_code   = $mnt_currency_code;
        $this->moneta_locale       = $moneta_locale;
        $this->isTestMode          = $testMode;
        $this->paymentPassword     = $paymentPassword;
        $this->submit_button_class = $submit_button_class;
        $this->submit_button_name  = $submit_button_name;

        $this->data = [
            'MNT_ID'                => $this->login,
            'MNT_TRANSACTION_ID'    => null,
            'MNT_AMOUNT'            => '',
            'MNT_CURRENCY_CODE'     => $this->mnt_currency_code,
            'MNT_SUBSCRIBER_ID'     => '',
            'MNT_TEST_MODE'         => $this->isTestMode,
        ];
    }

    public function getPaymentForm()
    {
        if ($this->data['MNT_AMOUNT'] <= 0) {
            throw new InvalidSumException();
        }

        if (empty($this->data['MNT_DESCRIPTION'])) {
            throw new EmptyDescriptionException();
        }

        if ($this->data['MNT_TRANSACTION_ID'] <= 0) {
            throw new InvalidInvoiceIdException();
        }

        $signature = '';
        foreach($this->data as $item)
        {
            $signature .= $item;
        }
        $signature .= $this->paymentPassword;

        $this->data['MNT_SIGNATURE'] = md5($signature);

        $string = "<form action='{$this->baseUrl}' method='post'>";
        foreach(array_merge($this->data, $this->customParams) as $name => $item)
        {
            $string .= "<input type='hidden' name='$name' value='$item'>";
        }
        $string .= "<input type='submit' class='{$this->submit_button_class}' name='{$this->submit_button_name}'></form>";

        return $string;
    }

    /**
     * Validates on ResultURL.
     *
     * @param  string $data query data
     *
     * @return bool
     */
    public function validateResult($data)
    {
        return $this->validate($data);
    }

    /**
     * Validates on SuccessURL.
     *
     * @param  string $data query data
     *
     * @return bool
     */
    public function validateSuccess($data)
    {
        return $this->validate($data, 'payment');
    }

    /**
     * Validates the Robokassa query.
     *
     * @param  string $data         query data
     * @param  string $passwordType type of password, 'validation' or 'payment'
     *
     * @return bool
     */
    private function validate($data, $passwordType = 'validation')
    {
        $this->data = $data;

        $signature = '';
        foreach($this->data as $item)
        {
            $signature .= $item;
        }
        $signature .= $this->paymentPassword;

        $this->valid = (md5($signature) === strtolower($data['MNT_SIGNATURE']));

        return $this->valid;
    }

    /**
     * Returns whether the Robokassa query is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return string
     */
    public function getSuccessAnswer() {
        return 'OK' . $this->getInvoiceId() . "\n";
    }


    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->data['MNT_TRANSACTION_ID'];
    }

    /**
     * @param $id
     *
     * @return Payment
     */
    public function setTransactionId($id)
    {
        $this->data['MNT_TRANSACTION_ID'] = (int) $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->data['MNT_AMOUNT'];
    }

    /**
     * @param  mixed $summ
     *
     * @throws InvalidSumException
     *
     * @return Payment
     */
    public function setSum($summ)
    {
        $summ = number_format($summ, 2, '.', '');

        if ($summ > 0) {
            $this->data['MNT_AMOUNT'] = $summ;

            return $this;
        } else {
            throw new InvalidSumException();
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->customParams['MNT_DESCRIPTION'];
    }

    /**
     * @param  string $description
     *
     * @return Payment
     */
    public function setDescription($description)
    {
        $this->customParams['MNT_DESCRIPTION'] = (string) $description;

        return $this;
    }

    public function getSubscriberId()
    {
        return $this->data['MNT_SUBSCRIBER_ID'];
    }

    public function setSubscriberId($id)
    {
        $this->data['MNT_SUBSCRIBER_ID'] = (int) $id;

        return $this;
    }
}
