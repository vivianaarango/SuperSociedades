<?php

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class ChatbotController extends ControllerBase {

    public function getAnswerAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "query",
        );

        $optional = array();

        if ($this->_checkFields($dataRequest, $fields, $optional)) {

            try {

                if (strlen(ControllerBase::ENDPOINTKEY) == 32) {

                    $result = $this->AnalyzeText(ControllerBase::ENDPOINT, ControllerBase::APPID, ControllerBase::ENDPOINTKEY, ControllerBase::TERM);
                    $result = json_decode($result);

                } else {

                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => false,
                        "message" => "Invalid LUIS key.",
                        "status" => ControllerBase::FAILED
                    ));
                }

            } catch (Exception $e) {
                $this->logError($e, $dataRequest);
            }
        }
    }

    function AnalyzeText($url, $app, $key, $query) {

        $headers = "Ocp-Apim-Subscription-Key: $key\r\n";
        $options = array ( 'http' => array (
                           'header' => $headers,
                           'method' => 'GET',
                           'cafile' => "/path/to/bundle/cacert.pem",
                           'verify_peer'=> true,
                           'verify_peer_name'=> true,
                           'ignore_errors' => true));
    
        $qs = http_build_query( array (
                "q" => $query,
                "verbose" => "false",
            )
        );

        $url = $url . $app . "?" . $qs;
        //print($url);

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
}
