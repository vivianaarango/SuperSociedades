<?php

use \Phalcon\Mvc\Model;

/**
 * 
 */
class General extends Model
{

    /**
     *
     * @var integer
     */
    public $id_general;

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
