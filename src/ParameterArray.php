<?php
namespace SeanKndy\Platypus;

class ParameterArray extends Parameter {
    public function __construct(array $parameters = []) {
        $this->name = 'item_array';
        $this->value = $parameters;
        return $this;
    }
    
    public function add(Parameter $param) {
        $this->parameters[] = $param;
        return $this;
    }
    
    public function setValue($value) {
        throw new \RuntimeException("Cannot call setValue() from ParameterArray, only Parameter");
    }
    
    public function __toString() {
        $str = "<{$this->name} type=\"array\">";
        foreach ($this->value as $param) {
            $str .= "<row>" . (string)$param . "</row>";
        }
        $str .= "</{$this->name}>";
        return $str;
    }
}
