<?php
namespace SeanKndy\Platypus;

class Parameter {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $value;

    public function __construct(string $name, $value, array $attributes = []) {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
    }

    /**
     * Set value
     *
     * @param string $value Value of parameter
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Set name of parameter
     *
     * @param string $name Name
     *
     * @return $this
     */
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Return name of parameter
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Return attributes as array
     *
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Represent parameter as XML
     *
     * @return string
     */
    public function __toString() {
        $str = "<{$this->name} ";
        foreach ($this->attributes as $key => $val) {
            $str .= "$key=\"$val\" ";
        }
        $str = rtrim($str) . ">" . $this->value . "</{$this->name}>";

        return $str;
    }
}
