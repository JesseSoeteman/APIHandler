<?php

namespace APIHandler\Auth;

use APIHandler\APIHandler;

class Auth {

    private APIHandler $apiHandler;

    public function __construct(APIHandler &$apiHandler, $project_id) {

        if (!isset($apiHandler)) {
            $this->apiHandler = new APIHandler();
            $this->apiHandler->addError("APIHandler not initialized", true);
        }

        $this->apiHandler = $apiHandler;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

    }

    public function InitEncryptSession() {
        $this->apiHandler->addError("Session not initialized", true);
    }

    public function GetSessionEncryptVariable() {

    }

    public function SetSessionEncryptVariable() {

    }

    public function CheckPermission() {
    }
}