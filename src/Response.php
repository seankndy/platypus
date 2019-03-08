<?php
namespace SeanKndy\Platypus;

class Response implements \Iterator {
    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $responseText, $responseCode;

    /**
     * @var boolean
     */
    protected $isSuccess;

    /**
     * @var array
     */
    protected $attributes = [];
    
    /**
     * @var int
     */
    private $position = 0;

    public function __construct(string $action, string $responseText, 
        string $responseCode, bool $isSuccess, array $attributes = []) {
        $this->action = $action;
        $this->responseText = $responseText;
        $this->responseCode = $responseCode;
        $this->isSuccess = $isSuccess;
        $this->attributes = $attributes;
    }

    public function getResponseText() {
        return $this->responseText;
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function isSuccess() {
        return $this->isSuccess;
    }

    public function getAction() {
        return $this->action;
    }
    
    public function getAttributes() {
        return $this->attributes;
    }
        
    public function setAction(string $action) {
        $this->action = $action;
        return $this;
    }
    
    /**
     * Parse XML into new Response object
     *
     * @param string $xml XML to parse
     *
     * @return Response
     */
    public static function fromXml(string $xml) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xml);
        if ($errors = libxml_get_errors()) {
            throw new \RuntimeException("Invalid XML.");
        }
        $action = (string)$xml->body->data_block->action;
        $responseText = !is_object($xml->body->data_block->response_text) ? (string)$xml->body->data_block->response_text : '';
        $responseCode = (string)$xml->body->data_block->response_code;
        $isSuccess = (string)$xml->body->data_block->is_success == '1' ? true : false;
        $attributes = [];
        if (property_exists($xml->body->data_block, 'attributes')) {
            foreach ($xml->body->data_block->attributes->data_block as $ele) {
                $vars = get_object_vars($ele);
                $attributes[] = $vars;
            }
        }
        return new self($action, $responseText, $responseCode, $isSuccess, $attributes);
    }

    /**
     * \Iterator implementation
     */
    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->attributes[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->attributes[$this->position]);
    }
}
