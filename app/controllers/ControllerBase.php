<?php

use Phalcon\Mvc\Controller;

/**
 *
 * @author Julián Arturo Molina Castiblanco
 * @version 0.1
 * @copyright Logisticapp.sa
 */
require __DIR__ . '/../../vendor/autoload.php';



class ControllerBase extends Controller {

    const SUCCESS = 200;
    const FAILED = 409;
    const FAILED_MESSAGE = "FAILED OPERATION";
    const SUCCESS_MESSAGE = "SUCCESS OPERATION";
    const APPID = "32c86169-3c1d-4e16-914b-d09a22635ddd";
    const ENDPOINTKEY = "d480de5181054b398561cfcfedac147a";
    const ENDPOINT = "https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/";
    const TERM = "turn on the left light";
   
    /**
     *
     */
    public function initialize() {
        $this->_dateTime = new \DateTime();
        $this->view->disable();
    }

    /**
     * Send Http JSON response
     */
    public function setJsonResponse($code, $msj, array $content) {
        $this->response->setStatusCode($code, $msj);
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function getJsonResponse() {
        if (isset($this->response)) {
            return $this->response->getContent();
        } else {
            return null;
        }
    }

    public function logError($e, $dataRequest) {

        $this->setJsonResponse(ControllerBase::FAILED, ControllerBase::FAILED_MESSAGE, array(
            "return" => false,
            "message" => "Error de la aplicación.",
            "status" => ControllerBase::FAILED,
            "cause" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine()
        ));
    }


    protected function _checkFields($dataRequest, array $fields, array $optional = array(), $method = "POST", $itemHead = 0) {

        $dataRequest = (array)$dataRequest;
        $check[] = array();
        $error = array();
        $i = 1;
        $item = null;

        foreach ($fields as $key => $value) {

            $rest = array_key_exists($value, $dataRequest);

            if ($rest) {

            } else {

                $check[] = "false";
                $error[] = empty($value) ? "" : $value;
                $item[] = $i;

            }
            $i++;
        }

        $item = $itemHead > 0 ? $itemHead : $item;

        if (array_search("false", $check)) {

            $this->setJsonResponse(self::SUCCESS, "CHECK FIELDS PARAMETER ERROR", array(
                "return" => false,
                "item" => $item,
                "messages" => array("This parameters are wrong" => array_unique($error)),
                "optional_fields" => $optional,
                "status" => self::FAILED
            ));

            return false;

        } else {

            foreach ($dataRequest as $key => $value) {

                if ($value == '')
                    $error[] = $key . " parameter is empty";
            }

            if (count($error) > 0) {

                $this->setJsonResponse(self::SUCCESS, "CHECK FIELDS PARAMETER ERROR", array(
                    "return" => false,
                    "item" => $item,
                    "messages" => array("This parameters are wrong" => array_unique($error)),
                    "optional_fields" => $optional,
                    "status" => self::FAILED
                ));

                return false;

            } else {

                return true;

            }
        }
    }
}
