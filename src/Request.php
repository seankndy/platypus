<?php
namespace SeanKndy\Platypus;

class Request {
    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $loginType = 'staff';

    /**
     * @var string
     */
    protected $username, $password;

    /**
     * Parameter or ParameterArray objects
     * @var array
     */
    protected $parameters = [];

    /**
     * Parameter objects
     * @var array
     */
    protected $properties = [];

    /**
     * Constructor
     *
     * @param string $username
     * @param string $password
     * @param string $action
     *
     * @return void
     */
    public function __construct(string $username, string $password, string $action) {
        $this->username = $username;
        $this->password = $password;
        $this->action = $action;
    }

    /**
     * Set login type
     *
     * @param string $type The type to set to
     *
     * @return $this
     */
    public function setLoginType(string $type) {
        $this->loginType = $type;
        return $this;
    }

    /**
     * Set username
     *
     * @param string $username Username
     *
     * @return $this
     */
    public function setUsername(string $username) {
        $this->username = $username;
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password Password
     *
     * @return $this
     */
    public function setPassword(string $password) {
        $this->password = $password;
        return $this;
    }

    /**
     * Set action
     *
     * @param string $action Action
     *
     * @return $this
     */
    public function setAction(string $action) {
        $this->action = $action;
        return $this;
    }

    /**
     * Add parameter to request
     *
     * @param Parameter $param Parameter to add
     *
     * @return $this
     */
    public function addParameter(Parameter $param) {
        foreach ($this->parameters as $k => $p) {
            if ($param->getName() == $p->getName()) {
                $this->parameters[$k] = $param;
                return $this;
            }
        }
        $this->parameters[] = $param;
        return $this;
    }

    /**
     * Add property to request
     *
     * @param Parameter $param Property to add
     *
     * @return $this
     */
    public function addProperty(Parameter $param) {
        foreach ($this->properties as $k => $p) {
            if ($param->getName() == $p->getName()) {
                $this->properties[$k] = $param;
                return $this;
            }
        }
        $this->properties[] = $param;
        return $this;
    }

    /**
     * Output this Request as an XML string
     *
     * @return $this
     */
    public function __toString() {
        $str  = '<?xml version="1.0"?>' . "\n";
        $str .= "<PLATXML>\n";
        $str .= "  <header></header>\n";
        $str .= "  <body>\n";
        $str .= "    <data_block>\n";
        $str .= "      <protocol>Plat</protocol>\n";
        $str .= "      <object>addusr</object>\n";
        $str .= "      <action>{$this->action}</action>\n";
        $str .= "      <username>{$this->username}</username>\n";
        $str .= "      <password>{$this->password}</password>\n";
        $str .= "      <logintype>{$this->loginType}</logintype>\n";
        $str .= "      <parameters>\n";
        foreach ($this->parameters as $param) {
            $str .= "        " . (string)$param . "\n";
        }
        $str .= "      </parameters>\n";
        $str .= "      <properties>\n";
        foreach ($this->properties as $param) {
            $str .= "        " . (string)$param . "\n";
        }
        $str .= "      </properties>\n";
        $str .= "   </data_block>\n";
        $str .= " </body>\n";
        $str .= "</PLATXML>\n";

        return $str;
    }
}
