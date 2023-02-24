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

        // Check if the session is already initialized, wich means that the client is already authenticated
        if (isset($_SESSION[$this->project_id . "_APIHANDLER_AUTH_shared_secret"])) {
            $this->apiHandler->addError("Session already initialized", true);
        }


        // Validate the client key
        if (!preg_match('/^04[0-9a-f]{128}$/', $client_publicKey)) {
            $this->apiHandler->addError("Client key is not valid, regex", true);
        }

        // Remove the 04 from the client key
        $client_publicKey = substr($client_publicKey, 2);

        // // Check if the client key does exist in the elliptic curve
        // if (!openssl_get_publickey($client_publicKey)) {
        //     $this->apiHandler->addError("Client key does not exist in the elliptic curve", true);
        // }

        // Generate a new private key
        $server_privateKey = openssl_pkey_new(array(
            "curve_name" => "prime256v1" // prime256v1 === secp256r1
        ));

        if (!$server_privateKey) {
            $this->apiHandler->addError("Private key could not be generated", true);
        }

        // $this->apiHandler->addData([
        //     "server_privateKey" => $server_privateKey,
        //     "client_publicKey" => $client_publicKey
        // ], true);

        // Create a shared secret, and check if it is valid
        $shared_secret = openssl_dh_compute_key(hex2bin($client_publicKey), $server_privateKey);
        if (!$shared_secret) {
            $error = "The shared secret could not be generated.";
            if (openssl_error_string()) {
                $error .= " " . openssl_error_string();
            } else {
                $error .= " No openssl error string.";
            }
            $this->apiHandler->addError($error, true);
        }

        // Save the shared secret in the session
        $server_encrypted_shared_secret = openssl_encrypt($shared_secret, "AES-256-CBC", hash("sha256", session_id()));
        if (!$server_encrypted_shared_secret) {
            $this->apiHandler->addError("The shared secret could not be encrypted.", true);
        }
        $_SESSION[$this->project_id . "_APIHANDLER_AUTH_shared_secret"] = $server_encrypted_shared_secret;

        // Encrypt the shared secret with the client public key
        if (!openssl_public_encrypt($shared_secret, $encrypted_shared_secret, $client_publicKey)) {
            $this->apiHandler->addError("Public key could not be encrypted", true);
        }

        // Return the encrypted shared secret
        return $encrypted_shared_secret;
    }

    public function DestroySession(): bool
    {
        if (!session_status() == PHP_SESSION_ACTIVE) {
            $this->apiHandler->addError("Session not active", true);
        }

        // Delete the session variables
        unset($_SESSION[$this->project_id . "_APIHANDLER_AUTH_shared_secret"]);

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
