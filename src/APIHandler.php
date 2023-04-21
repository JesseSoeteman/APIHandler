<?php

namespace APIHandler;

use APIHandler\Classes\RequestMethod;

/**
 * APIHandler class to handel the status of an API call.
 * 
 * @author  Jesse Soeteman
 * @version 1.0
 * @since   2022-12-24
 */
class APIHandler
{
    /** 
     * @var array $errors The errors that occurred during the API call.
     */
    private array $errors = [];
    /** 
     * @var $data The data that was returned by the API call.
     */
    private array $data = [];
    /** 
     * @var string $request_method The request type of the API call.
     */
    private string $request_method;

    public function __construct($request_method = RequestMethod::GET)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        $this->request_method = $request_method;
        if ($_SERVER['REQUEST_METHOD'] != $this->request_method) {
            $this->addError("Request method needs to be {$this->request_method}", true);
        }
    }

    /**
     * Add an error to the errors array.
     * 
     * @param string|array $error The error to add.
     * @param bool $exit Whether to exit the API call.
     */
    public function addError(string | array $error, bool $exit = false)
    {
        // Adding the error of errors to the errors array.
        if (is_array($error) && !empty($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else if (is_string($error) && !empty($error)){
            $this->errors[] = $error;
        }

        // exit the api call if the exit parameter is true.
        if ($exit) {
            $this->Exit();
        }
    }

    /**
     * Add data to the data object.
     * 
     * @param object $data The data to add.
     * @param bool $exit Whether to exit the API call.
     */
    public function addData($data, bool $exit = false)
    {
        // Append the data to the data array.
        $this->data[] = $data;
        // $this->data = $data;
        if ($exit) {
            $this->Exit();
        }
    }

    /**
     * Exits if there is an error.
     * 
     */
    public function ExitOnError()
    {
        // Checks if there are any errors.
        if (!empty($this->errors)) {
            $this->Exit();
        }
    }

    /**
     * Return the result of the API call.
     */
    public function Exit()
    {
        // Return the result of the API call.
        $result = [];
        $result['status'] = "error";
        // set the status to success and add the data to the result if there are no errors.
        if (empty($this->errors)) {
            $result['status'] = "success";
            $result['data'] = $this->data;
            echo json_encode($result);
            exit();
        }
        $result['errors'] = $this->errors;
        echo json_encode($result);
        exit();
    }
}
