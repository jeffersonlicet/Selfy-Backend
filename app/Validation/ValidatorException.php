<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/24/2017
 * Time: 3:01 PM
 */

namespace App\Validation;

use RuntimeException;

class ValidatorException extends RuntimeException
{
    /**
     * @var \Illuminate\Support\Collection|array
     */
    protected $messages;


    /**
     * ValidationException constructor.
     * @param string $messages
     */
    public function __construct($messages)
    {
        $this->messages = is_array($messages) ? collect($messages) : $messages;
    }

    /**
     * @return array|\Illuminate\Support\Collection|string
     */
    public function getMessageBag()
    {
        return $this->messages;
    }
}