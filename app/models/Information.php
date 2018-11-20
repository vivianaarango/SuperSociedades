<?php

use \Phalcon\Mvc\Model;

/**
 * 
 */
class Information extends Model
{

    /**
     *
     * @var integer
     */
    public $id_information;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $description;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
    }

}
