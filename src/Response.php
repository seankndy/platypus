<?php
namespace SeanKndy\Platypus;

class Response implements \Iterator
{
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

    /**
     *
     *
     * @return void
     */
    public function __construct(string $action, string $responseText,
        string $responseCode, bool $isSuccess, array $attributes = [])
    {
        $this->action = $action;
        $this->responseText = $responseText;
        $this->responseCode = $responseCode;
        $this->isSuccess = $isSuccess;
        $this->attributes = $attributes;
    }

    /**
     * Get response text, may be blank
     *
     * @return string
     */
    public function getResponseText()
    {
        return $this->responseText;
    }

    /**
     * Get response code
     *
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Is success flag set?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * Get action
     *
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get all attributes as array
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAction(string $action)
    {
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
    public static function fromXml(string $xml)
    {
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
                $attributes[] = self::xmlToArray($ele);
            }
        }
        return new self($action, $responseText, $responseCode, $isSuccess, $attributes);
    }

    /**
     * Recursively convert SimpleXMLElement to key/val array
     *
     * @param \SimpleXMLElement $xmlObject
     * @return array
     */
    private static function xmlToArray(\SimpleXMLElement $xmlObject)
    {
        $arr = [];
        foreach ($xmlObject->children() as $r) {
            $t = [];
            if (\count($r->children()) == 0) {
                $arr[$r->getName()] = \strval($r);
            } else {
                $arr[$r->getName()][] = self::xmlToArray($r);
            }
        }
        return $arr;
    }

    /**
     * \Iterator implementation, for iterating over $this->attributes
     */
    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->attributes[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->attributes[$this->position]);
    }
}
