<?php
namespace SeanKndy\Platypus;

/**
 * Like Parameter, but $this->value becomes an array of Parameters
 */
class ParameterArray extends Parameter {
    public function __construct(array $parameters = []) {
        parent::__construct('item_array', $parameters, []);
    }

    /**
     * Add Parameter to this array
     *
     * @param Parameter $param Parameter to add
     *
     * @return $this
     */
    public function add(Parameter $param) {
        if (!is_array($this->value))
            $this->value = [];
        $this->value[] = $param;
        return $this;
    }

    /**
     * Return XML representation (string) of this paraemter array
     *
     * @return string
     */
    public function __toString() {
        $str = "<{$this->name} type=\"array\">";
        foreach ($this->value as $param) {
            $str .= "<row>" . (string)$param . "</row>";
        }
        $str .= "</{$this->name}>";
        return $str;
    }
}
