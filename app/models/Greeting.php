<?php

use \Phalcon\Mvc\Model;

/**
 * 
 */
class Greeting extends Model
{

    /**
     *
     * @var integer
     */
    public $id_greeting;

    /**
     *
     * @var string
     */
    public $initial_hour;

    /**
     *
     * @var string
     */
    public $finish_hour;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var String
     */
    public $type;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
    }

}
