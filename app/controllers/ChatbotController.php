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

                    $_query = $this->removeAccents($dataRequest->query);
                    $result = $this->AnalyzeText(ControllerBase::ENDPOINT, ControllerBase::APPID, ControllerBase::ENDPOINTKEY, $_query);
                    $result = json_decode($result);

                    $intent = $result->topScoringIntent;
                    $date = $this->_dateTime->format('H:i:s');
                    $answer = "";

                    switch ($intent->intent) {

                        case "Greeting":

                            $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                            $answer = Greeting::findFirst(array(
                                "conditions" => 
                                "type = ?1 and ((initial_hour <= ?2 and finish_hour >= ?2) or (initial_hour is null and finish_hour is null)) ",
                                "bind" => array(1 => $entities,
                                                2 => $date)
                            ));

                            if (isset($answer->id_greeting))
                                $answer = $answer->description;
                            else 
                                $answer = ControllerBase::ANSWER_FAILURE;

                            break;

                        case "Information":

                            $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                            $answer = Information::findFirst(array(
                                "conditions" => "type = ?1",
                                "bind" => array(1 => $entities)
                            ));

                            if (isset($answer->id_information))
                                $answer = $answer->description;
                            else 
                                $answer = ControllerBase::ANSWER_FAILURE;

                            break;  

                        case "General":

                            $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                            $answer = General::find(array(
                                "conditions" => "type = ?1",
                                "bind" => array(1 => $entities)
                            ));

                            $count = count($answer);

                            if ($count > 0){
                                $pos = rand(0, $count-1);
                                $answer = $answer[$pos]->description;
                            } else {
                                $answer = ControllerBase::ANSWER_FAILURE;
                            }

                            break;      

                        case "None":
                            $answer = ControllerBase::ANSWER_FAILURE;
                            break;    
                    }


                    $data['question'] = $dataRequest->query;
                    $data['answer'] = $answer; 

                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => true,
                        "message" => "Operation Success",
                        "data" => $data,
                        "status" => ControllerBase::SUCCESS
                    ));

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

    public function AnalyzeText($url, $app, $key, $query) {

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

    public function removeAccents($cadena)
    {
   
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );
    
        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );
    
        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );
    
        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );
    
        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );
    
        // $cadena = str_replace(
        //     array('ñ', 'Ñ', 'ç', 'Ç'),
        //     array('n', 'N', 'c', 'C'),
        //     $cadena
        // );        
    
        return $cadena;
    }
}
