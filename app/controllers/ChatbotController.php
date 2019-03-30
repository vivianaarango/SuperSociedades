<?php


class ChatbotController extends ControllerBase {

    /*
    * Obtener respuesta a la pregunta
    */
    public function getAnswerAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "query",
            "id_chat"
        );

        $optional = array();

        if ($this->_checkFields($dataRequest, $fields, $optional)) {

            try {

                if (strlen(ControllerBase::ENDPOINTKEY) == 32) {
                    
                    //addquestion[supersociedades*]=cual es tu nombre/me llamo pepito

                    $_query = $this->removeAccents($dataRequest->query);
                    
                    /*$validate = (explode("=", $_query));
                    $_key = $validate[0];
                    $key = "addquestion[".ControllerBase::PASSWORD."]";
                    
                    if ($key == $_key){
                        
                        if ($this->addQuestion($validate[1])){
                            $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                                "return" => true,
                                "message" => "Se ha agregado la pregunta.",
                                "status" => ControllerBase::SUCCESS
                            ));
                        } else {
                            $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                                "return" => true,
                                "message" => "No se ha podido agregar la pregunta.",
                                "status" => ControllerBase::SUCCESS
                            ));
                        }
                        
                    } else {*/
                        
                        $result = $this->AnalyzeText(ControllerBase::ENDPOINT, ControllerBase::APPID_1, ControllerBase::ENDPOINTKEY, $_query);
                        $result = json_decode($result);
                        
                        //print_r($result);die;
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
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
    
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
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
    
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
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                }
    
                                break;    
    
                            case "Process":
                                
                                if ($intent->score > 30){
                                    $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                                    $answer = Procesos::findFirst(array(
                                        "conditions" => "type = ?1",
                                        "bind" => array(1 => $entities)
                                    ));
        
                                    if (isset($answer->id_proceso))
                                        $answer = $answer->description;
                                    else 
                                        $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                } else {
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                }
                                
                                break;    
                                
                            case "Sociedades":
    
                                $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                                $answer = Sociedades::findFirst(array(
                                    "conditions" => "type = ?1",
                                    "bind" => array(1 => $entities)
                                ));
                                
    
                                if (isset($answer->id))
                                    $answer = $answer->description;
                                else 
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                break;  

                            case "Consult":
                                
                                $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                                $answer = Consult::findFirst(array(
                                    "conditions" => "type = ?1",
                                    "bind" => array(1 => $entities)
                                ));
    
                                
                                if (isset($answer->id_consult))
                                    $answer = $answer->description;
                                else 
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                break; 

                            case "Concepts":
                                
                                $entities = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                                $answer = Concepts::findFirst(array(
                                    "conditions" => "type = ?1",
                                    "bind" => array(1 => $entities)
                                ));
    
                                
                                if (isset($answer->id_concept))
                                    $answer = $answer->description;
                                else 
                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                break;  

                            case "AuxiliaryJustice":
                                
                                $entities = $result->entities; 
                               // print_r($result);die;
                                $entities_1 = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
                                $entities_2 = isset($result->entities[1]->type) ? $result->entities[1]->type : null;
                                $entities_3 = isset($result->entities[2]->type) ? $result->entities[2]->type : null;
                                $entities_4 = isset($result->entities[3]->type) ? $result->entities[3]->type : null;

                                $auxiliary_justice = new AuxiliaryJustice();
                                
                                if ($entities_2 == 'Deberes' || $entities_2 == 'Promotor' || $entities_3 == 'SuperIntendencia' || $entities_4 == 'SinParticipar'){
                                    
                                    if($entities_2 == 'Deberes'){
                                        $entity_1 = $entities_2;
                                        $entity_2 = $entities_3;
                                    } elseif ($entities_4 == 'SinParticipar') {
                                        $entity_1 = $entities_3;
                                        $entity_2 = $entities_4;
                                    } else {
                                        $entity_1 = $entities_1;
                                        $entity_2 = $entities_3;
                                    }
                                    
                                    $answer = $auxiliary_justice->getAnswer($entity_1, $entity_2);
                                    $answer = $answer->fetchAll();
                                    
                                    if (count($answer)>0){
                                        $answer = $answer[0]['description'];
                                    } else {
                                        $answer = $auxiliary_justice->getAnswer($entities_1, $entities_2);
                                        $answer = $answer->fetchAll();
        
                                        if (count($answer)>0){
                                            $answer = $answer[0]['description'];
                                        } else {
                                            $answer = $auxiliary_justice->getAnswer($entities_2, $entities_1);
                                            $answer = $answer->fetchAll();
                                                
                                            if (count($answer)>0){
                                                $answer = $answer[0]['description'];
                                            } else {
                                                $answer = $auxiliary_justice->getAnswer($entities_1, $entities_3);
                                                $answer = $answer->fetchAll();
                                                
        
                                                if (count($answer)>0){
                                                    $answer = $answer[0]['description'];
                                                } else {
                                                    $answer = $auxiliary_justice->getAnswer($entities_2, $entities_3);
                                                    $answer = $answer->fetchAll();
                                                    
                                                    if (count($answer)>0)
                                                        $answer = $answer[0]['description'];
                                                    else 
                                                        $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                                }
                                            }
                                        }
                                    } 
                                } else {
                                    $answer = $auxiliary_justice->getAnswer($entities_1, $entities_2);
                                $answer = $answer->fetchAll();

                                    if (count($answer)>0){
                                        $answer = $answer[0]['description'];
                                    } else {
                                        $answer = $auxiliary_justice->getAnswer($entities_2, $entities_1);
                                        $answer = $answer->fetchAll();
                                            
                                        if (count($answer)>0){
                                            $answer = $answer[0]['description'];
                                        } else {
                                            $answer = $auxiliary_justice->getAnswer($entities_1, $entities_3);
                                            $answer = $answer->fetchAll();
                                            
    
                                            if (count($answer)>0){
                                                $answer = $answer[0]['description'];
                                            } else {
                                                $answer = $auxiliary_justice->getAnswer($entities_2, $entities_3);
                                                $answer = $answer->fetchAll();
                                                
                                                if (count($answer)>0)
                                                    $answer = $answer[0]['description'];
                                                else 
                                                    $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);
                                            }
                                        }
                                    }
                                } 
  
                                break;      
                            
                            case "None":
                                
                                $answer = $this->findStaticQuestion($_query, $dataRequest->id_chat);

                                break;    
                        }

                        $history = new History;
                        $history->id_chat = $dataRequest->id_chat;
                        $history->question = $dataRequest->query;
                        $history->answer = $answer;
                        $history->date = date("Y-m-d H:i:s"); 
                        $history->save();
    
    
                        $data['question'] = $dataRequest->query;
                        $data['answer'] = $answer; 
    
                        $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                            "return" => true,
                            "message" => "Operation Success",
                            "data" => $data,
                            "status" => ControllerBase::SUCCESS
                        ));
                    //}

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
    
    /*
    * Agregar usuario y generar solicitud de chat
    */
    public function addUserAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "name",
            "department",
            "phone",
            "email",
            "city"
        );

        if ($this->_checkFields($dataRequest, $fields)) {

            try {
                
                $user = UserChat::findFirst(array(
                    "conditions" => "email = ?1",
                    "bind" => array(1 => $dataRequest->email)
                ));
                
                if (isset($user->id_user)){
                    
                    $new_chat = new Request;
                    $new_chat->id_user = $user->id_user;
                    $new_chat->date = date("Y-m-d H:i:s");  
                    $new_chat->save();
                    
                    $data[] =  array(
                        "id_user" => $user->id_user,
                        "name" => $user->name,
                        "id_chat" => $new_chat->id_chat
                    );
                        
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => true,
                        "message" => "Usuario existente.",
                        "data" => $data,
                        "status" => ControllerBase::SUCCESS
                    ));
                    
                } else {
                    $usuario = new UserChat;
                    $usuario->name = $dataRequest->name;
                    $usuario->department = $dataRequest->department;
                    $usuario->city = $dataRequest->city;
                    $usuario->phone = $dataRequest->phone;
                    $usuario->email = $dataRequest->email;
                    
                    if ($usuario->save()) {
                      
                        $new_chat = new Request;
                        $new_chat->id_user = $usuario->id_user;
                        $new_chat->date = date("Y-m-d H:i:s");  
                        $new_chat->save();
                        //print_r($new_chat);die;
                        $data[] =  array(
                            "id_user" => $usuario->id_user,
                            "name" => $usuario->name,
                            "id_chat" => $new_chat->id_chat
                        );
    
                        $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                            "return" => true,
                            "message" => "Se ha guardado el usuario satisfactoriamente.",
                            "data" => $data,
                            "status" => ControllerBase::SUCCESS
                        ));
                        
                    } else {
                        
                        $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                            "return" => false,
                            "message" => "No se pudo guardar la informacion del usuario.",
                            "status" => ControllerBase::FAILED
                        ));
                        
                    }
                }
                
            } catch (Exception $e) {
                $this->logError($e, $dataRequest);
            }
        }
    }

    /*
    * Historial conversacion
    */
    public function historyAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "email_user"
        );

        if ($this->_checkFields($dataRequest, $fields)) {

            try {

                $historial_user = new History;
                $result = ($historial_user->getHistoryByUser($dataRequest->email_user));
              
                $result = $result->fetchAll();
                if (count($result) > 0) {
                    $history = History::find(array(
                        "conditions" => "id_chat = ?1",
                        "bind" => array(1 => $result[0]['id_chat'])
                    ));
                    
                    if (count($history) > 0) {
                        $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                            "return" => true,
                            "message" => "Historial.",
                            "data" => $history,
                            "status" => ControllerBase::SUCCESS
                        ));
                    } else {
                        $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                            "return" => false,
                            "message" => "No se ha encontrado conversación con este usuario.",
                            "status" => ControllerBase::SUCCESS
                        ));                    
                    }
                } else {
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => false,
                        "message" => "No se ha encontrado conversación con este usuario.",
                        "status" => ControllerBase::SUCCESS
                    )); 
                }
                
            } catch (Exception $e) {
                $this->logError($e, $dataRequest);
            }
        }
    }
    
    /*
    *Listar preguntas no encontradas 
    */
    public function notFoundQuestionsAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "password"
        );

        if ($this->_checkFields($dataRequest, $fields)) {

            try {

                if (ControllerBase::PASSWORD == $dataRequest->password) {
                    $data = NotFoundQuestions::find(array(
                        "conditions" => "count >= ?1",
                        "bind" => array(1 => 2)
                    ));

                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => true,
                        "message" => "Preguntas no encontradas.",
                        "data" => $data,
                        "status" => ControllerBase::SUCCESS
                    ));
                } else {
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => false,
                        "message" => "Error de autenticacion.",
                        "status" => ControllerBase::FAILED
                    ));  
                }
        
            } catch (Exception $e) {
                $this->logError($e, $dataRequest);
            }
        }
    }
    
    /*
    *Validar clave de acceso
    */
    public function validatePasswordAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "pass"
        );

        if ($this->_checkFields($dataRequest, $fields)) {

            try {

                if (ControllerBase::PASSWORD == $dataRequest->pass) {
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => true,
                        "message" => "Clave de acceso validada.",
                        "status" => ControllerBase::SUCCESS
                    ));
                } else {
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => false,
                        "message" => "Error de autenticacion.",
                        "status" => ControllerBase::FAILED
                    ));  
                }
        
            } catch (Exception $e) {
                $this->logError($e, $dataRequest);
            }
        }
    }
    
    /*
    *Agregar pregunta 
    */
    public function addQuestionAction() {

        $dataRequest = $this->request->getJsonPost();

        $fields = array(
            "question",
            "answer"
        );

        if ($this->_checkFields($dataRequest, $fields)) {

            try {
                
                $question = new ExtraQuestions;
                $question->question = $dataRequest->question;
                $question->answer = $dataRequest->answer;

                if ($question->save()){
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => true,
                        "message" => "Se ha agregado la pregunta.",
                        "status" => ControllerBase::SUCCESS
                    ));
                } else {
                    $this->setJsonResponse(ControllerBase::SUCCESS, ControllerBase::SUCCESS_MESSAGE, array(
                        "return" => true,
                        "message" => "No se ha podido agregar la pregunta.",
                        "status" => ControllerBase::SUCCESS
                    ));
                }
                        
               
            } catch (Exception $e) {
                $this->logError($e, $dataRequest);
            }
        }
    }
    
    /*
    * Respuesta api LUIS
    */
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

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
    
    /*
    * Agregar pregunta estatica 
    */
    public function addQuestion($data) {
        
        $res = (explode("/", $data));
        $question = new ExtraQuestions;
        $question->question = strtolower($res[0]);
        $question->answer = $res[1];
        
        if ($question->save()) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * Quitar acentos
    */
    public function removeAccents($cadena){
   
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

        $cadena = str_replace(
            array('?', '¿'),
            array('', ''),
            $cadena );
    
        return $cadena;
    }

    /*
    * Busca la pregunta en la tabla de preguntas estaticas
    */
    public function findStaticQuestion($query, $id_chat) {
        
        $answer = $this->findAnswer($query);
  
        if ($answer != false)
            return $answer;
            
        $question = new ExtraQuestions;
        $query = strtolower($query);
        $result = ($question->findQuestion($query));
        $result = $result->fetchAll();

        if (count($result) > 0 ){
            return $result[0]['answer'];
        } else {
            $not_found_questions = NotFoundQuestions::findFirst(array(
                "conditions" => "question = ?1",
                "bind" => array(1 => $query)
            ));
            
            if (isset($not_found_questions->id)){
                $not_found_questions->count = $not_found_questions->count+1; 
            } else {
                $not_found_questions = new NotFoundQuestions;
                $not_found_questions->question = $query;
                $not_found_questions->id_chat = $id_chat;
                $not_found_questions->count = 1; 
            }
            
            $not_found_questions->save();
            
            $history = History::findFirst(array(
                "conditions" => "id_chat = ?1",
                "bind" => array(1 => $id_chat),
                "order" => "id DESC"
            ));
            
            if( isset($history->id)){
                if ($history->answer == ControllerBase::ANSWER_FAILURE || $history->answer == ControllerBase::ANSWER_FAILURE_ERROR)
                    return ControllerBase::ANSWER_FAILURE_ERROR;
                else 
                    return ControllerBase::ANSWER_FAILURE;
            }
        }
    }

    /*
    * Consultar pregunta en SuperSociedades1
    */
    public function findAnswer($query){
        
        $result = $this->AnalyzeText(ControllerBase::ENDPOINT, ControllerBase::APPID_2, ControllerBase::ENDPOINTKEY, $query);
        $result = json_decode($result);
        $intent = $result->topScoringIntent;
        //print_r($result);die;

        if ($intent->intent == 'Insolvencia') {

            $entities = $result->entities; 

            $entities_1 = isset($result->entities[0]->type) ? $result->entities[0]->type : null;
            $entities_2 = isset($result->entities[1]->type) ? $result->entities[1]->type : null;
            $entities_3 = isset($result->entities[2]->type) ? $result->entities[2]->type : null;

            $insolvencia = new Insolvencia();
            $answer = $insolvencia->getAnswer($entities_1, $entities_2);
            $answer = $answer->fetchAll();
            //print_r($answer);die;
            if (count($answer)>0){
                return $answer = $answer[0]['description'];
            } else {
                $answer = $insolvencia->getAnswer($entities_2, $entities_1);
                $answer = $answer->fetchAll();
                    
                if (count($answer)>0){
                    return $answer = $answer[0]['description'];
                } else {
                    $answer = $insolvencia->getAnswer($entities_1, $entities_3);
                    $answer = $answer->fetchAll();

                    if (count($answer)>0){
                        return $answer = $answer[0]['description'];
                    } else {
                        $answer = $insolvencia->getAnswer($entities_2, $entities_3);
                        $answer = $answer->fetchAll();
                        
                        if (count($answer)>0){
                            return $answer = $answer[0]['description'];
                        } else {
                            $answer = $insolvencia->getAnswer($entities_3, $entities_1);
                            $answer = $answer->fetchAll();
        
                            if (count($answer)>0){
                                return $answer = $answer[0]['description'];
                            } else {
                                return false;
                            }
                        }
                    }
                }
            } 
        } else {
            return false;
        }
    }
}