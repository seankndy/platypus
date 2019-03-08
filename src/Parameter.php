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
    protected $attributes;

    /**
     * @var string
     */
    protected $value;
    
    public function __construct(string $name, string $value, array $attributes = []) {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        return $this;
    }
    
    public function setValue(string $value) {
        $this->value = $value;
        return $this;
    }
    
    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }
    
    public function __toString() {
        $str = "<{$this->name} ";
        foreach ($this->attributes as $key => $val) {
            $str .= "$key=\"$val\" ";
        }
        $str = rtrim($str) . ">" . $this->value . "</{$this->name}>";
        return $str;
    }
}

