<?php

define('get_request', 'GET');
define('post_request', 'POST');

/**
 * APIReturn class to return the result of an API call.
 * 
 * @author  Jesse Soeteman
 * @version 1.0
 * @since   2022-12-24
 */
class APIReturn
{
    /** 
     * @var array $errors The errors that occurred during the API call.
     */
    private array $errors = [];
    /** 
     * @var object $data The data that was returned by the API call.
     */
    private object $data;

    public function __construct($request_type = get_request)
    {
        $this->request_type = $request_type;
        if ($_SERVER['REQUEST_METHOD'] != $this->request_type) {
            $this->addError("Request method needs to be {$this->request_type}", true);
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

        if (is_array($error) && !empty($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else if (is_string($error) && !empty($error)){
            $this->errors[] = $error;
        }

        if ($exit) {
            $this->APIExit();
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
        $this->data = $data;
        if ($exit) {
            $this->APIExit();
        }
    }

    public function APIExitOnError()
    {
        if (!empty($this->errors)) {
            $this->APIExit();
        }
    }

    /**
     * Return the result of the API call.
     */
    public function APIExit()
    {
        $result = [];
        $result['status'] = "error";
        if (empty($this->errors)) {
            $result['status'] = "success";
            if (!empty($this->data)) {
                $result['data'] = $this->data;
            }
            
            echo json_encode($result);
            exit();
        }
        $result['errors'] = $this->errors;
        echo json_encode($result);
        exit();
    }
}
