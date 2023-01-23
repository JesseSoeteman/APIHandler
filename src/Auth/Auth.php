<?php

namespace APIHandler\Auth;

abstract class Auth {

    public function InitSession() {
        echo "InitSession";
    }

    public function GetSessionVariable() {
    }

    public function SetSessionVariable() {
    }

    public function CheckPermission() {
    }
}