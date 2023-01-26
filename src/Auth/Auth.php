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

        if (!isset($apiHandler)) {
            $this->apiHandler = new APIHandler();
            $this->apiHandler->addError("APIHandler not initialized", true);
        }

        $this->apiHandler = $apiHandler;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function InitEncryptSession($clientKey = null)
    {
        // Check if client key is set
        if (!isset($clientKey)) {
            $this->apiHandler->addError("Client key not set", true);
        }

        // Validate the client key
        if (!preg_match('/^[a-f0-9]{64}$/', $clientKey)) {
            $this->apiHandler->addError("Client key is not valid", true);
        }

        if (isset($_SESSION[$this->project_id . "APIHANDLER_AUTH_privateKey"])) {
            $this->apiHandler->addError("Session already initialized", true);
        }

        // Check if the client key does exist in the elliptic curve
        if (!openssl_get_publickey($clientKey)) {
            $this->apiHandler->addError("Client key does not exist in the elliptic curve", true);
        }

        // Generate a new private key
        $privateKey = openssl_pkey_new(array(
            "curve_name" => "secp256k1",
        ));

        if (!$privateKey) {
            $this->apiHandler->addError("Private key could not be generated", true);
        }

        // Get the private key
        if (openssl_pkey_export($privateKey, $privateKey)) {
            $this->apiHandler->addError("Private key could not be exported", true);
        }

        // Get the public key
        $publicKey = openssl_pkey_get_details($privateKey);
        if (!$publicKey) {
            $this->apiHandler->addError("Public key could not be generated", true);
        }

        // Encrypt the public key with the client key
        if (!openssl_public_encrypt($publicKey["key"], $encryptedPublicKey, $clientKey)) {
            $this->apiHandler->addError("Public key could not be encrypted", true);
        }

        // Set the session variables
        $_SESSION[$this->project_id . "_APIHANDLER_AUTH_privateKey"] = $privateKey;

        // Return the encrypted public key
        return $encryptedPublicKey;
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
