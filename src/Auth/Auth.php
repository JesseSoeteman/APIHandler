<?php

namespace APIHandler\Auth;

use APIHandler\APIHandler;

class Auth
{

    private APIHandler $apiHandler;
    private string $project_id;

    public function __construct(APIHandler &$apiHandler, $project_id = "default")
    {
        $this->project_id = $project_id;

        // if (!isset($apiHandler)) {
        //     $this->apiHandler = new APIHandler();
        //     $this->apiHandler->addError("APIHandler not initialized", true);
        // }
        if ($apiHandler instanceof APIHandler) {
            $this->apiHandler = $apiHandler;
        } else {
            $this->apiHandler = new APIHandler();
            $this->apiHandler->addError("APIHandler not initialized", true);
        }

        $this->apiHandler = $apiHandler;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function InitEncryptSession($client_publicKey = null)
    {
        // Check if client key is set
        if (!isset($client_publicKey)) {
            $this->apiHandler->addError("Client key not set", true);
        }

        // Validate the client key
        if (!preg_match('/^04[0-9a-f]{128}$/', $client_publicKey)) {
            $this->apiHandler->addError("Client key is not valid, regex", true);
        }

        // Remove the first 2 characters
        $client_publicKey = substr($client_publicKey, 2);

        // Check if the session is already initialized, wich means that the client is already authenticated
        if (
            isset($_SESSION[$this->project_id . "APIHANDLER_AUTH_client_publicKey"]) ||
            isset($_SESSION[$this->project_id . "APIHANDLER_AUTH_privateKey"]) ||
            isset($_SESSION[$this->project_id . "APIHANDLER_AUTH_publicKey"])
        ) {
            $this->apiHandler->addError("Session already initialized", true);
        }

        // Check if the client key does exist in the elliptic curve
        if (!openssl_get_publickey($client_publicKey)) {
            $this->apiHandler->addError("Client key does not exist in the elliptic curve", true);
        }

        // Generate a new private key
        $server_privateKey = openssl_pkey_new(array(
            "curve_name" => "secp256r1",
        ));

        if (!$server_privateKey) {
            $this->apiHandler->addError("Private key could not be generated", true);
        }

        // Get the private key
        if (openssl_pkey_export($server_privateKey, $server_privateKey)) {
            $this->apiHandler->addError("Private key could not be exported", true);
        }

        // Get the public key
        $server_publicKey = openssl_pkey_get_details($server_privateKey);
        if (!$server_publicKey) {
            $this->apiHandler->addError("Public key could not be generated", true);
        }

        // Encrypt the public key with the client key
        if (!openssl_public_encrypt($server_publicKey["key"], $server_encryptedPublicKey, $client_publicKey)) {
            $this->apiHandler->addError("Public key could not be encrypted", true);
        }

        // Set the session variables
        $_SESSION[$this->project_id . "_APIHANDLER_AUTH_client_publicKey"] = $client_publicKey;
        $_SESSION[$this->project_id . "_APIHANDLER_AUTH_server_privateKey"] = $server_privateKey;
        $_SESSION[$this->project_id . "_APIHANDLER_AUTH_server_publicKey"] = $server_publicKey["key"];

        // Return the encrypted public key
        return $server_encryptedPublicKey;
    }

    public function DestroySession(): bool
    {
        if (!session_status() == PHP_SESSION_ACTIVE) {
            $this->apiHandler->addError("Session not active", true);
        }

        // Delete the session variables
        unset($_SESSION[$this->project_id . "_APIHANDLER_AUTH_client_publicKey"]);
        unset($_SESSION[$this->project_id . "_APIHANDLER_AUTH_server_privateKey"]);
        unset($_SESSION[$this->project_id . "_APIHANDLER_AUTH_server_publicKey"]);

        // Destroy the session
        if (!session_destroy()) {
            $this->apiHandler->addError("Session could not be destroyed", true);
        }

        return true;
    }

    public function GetSessionEncryptVariable()
    {
    }

    public function SetSessionEncryptVariable()
    {
    }

    public function CheckPermission()
    {
    }
}
