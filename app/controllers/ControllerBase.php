<?php

use Phalcon\Mvc\Controller;


require __DIR__ . '/../../vendor/autoload.php';



class ControllerBase extends Controller {

    const SUCCESS = 200;
    const FAILED = 409;
    const FAILED_MESSAGE = "FAILED OPERATION";
    const SUCCESS_MESSAGE = "SUCCESS OPERATION";
    const APPID_1 = "036582b0-bae3-4817-9d08-61cb0edb2b74"; //SuperSociedades
    const APPID_2 = "18e18147-0907-42d6-9ff3-7f885ad7843e"; //SuperSociedades1
    const ENDPOINTKEY = "0b2b3577deaa4603b038390ca10ad863";
    //const ENDPOINTKEY_2 = "d480de5181054b398561cfcfedac147a"; //SuperSociedades1
    const ENDPOINT = "https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/";
    const TERM = "turn on the left light";
    const PASSWORD = "supersociedades*";
    const ANSWER_FAILURE = 'No he logrado entender la pregunta.¿Podria repetirla por favor?';
    const ANSWER_FAILURE_ERROR = "En este caso concreto, se necesitaría un análisis de la normatividad, cabe destacar que este chat es un medio de consultas, limitado a entregar información de carácter general de servicios y funciones de la Superintendencia de Sociedades, que no remplaza los canales de comunicación estipulados en la legislación vigente, de tal modo que las respuestas tendrán carácter meramente informativo y no constituyen un acto administrativo. Solo es un punto de orientación, en ese caso y en vista de la dificultad de la consulta, usted deberá elevarla por escrito a esta Superintendencia, en el correo: webmaster@supersociedades.gov.co Para que la entidad le  indique a través de un concepto.";
   
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
