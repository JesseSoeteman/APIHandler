<?php

namespace APIHandler\Auth;

use APIHandler\APIHandler;

class Auth {

    private APIHandler $apiHandler;

    public function __construct(APIHandler &$apiHandler) {

        if (!isset($apiHandler)) {
            $this->apiHandler = new APIHandler();
            $this->apiHandler->addError("APIHandler not initialized", true);
        }

        $this->apiHandler = $apiHandler;
    }

    public function InitSession() {
        $this->apiHandler->addError("Session not initialized", true);
    }

    public function GetSessionVariable() {
    }

    public function SetSessionVariable() {
    }

    public function CheckPermission() {
    }
}