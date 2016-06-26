<?php

  /**
   * @author: Kevin Olinger, 2016-06-21
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-25
   */

  namespace phredUNO\action;
  use phredUNO\Core;

  class Action {

    public $client = 0;
    public $obj = array();
    public $token = "";

    protected $valid = true;
    protected $params = array();

    public function __construct($obj, $client) {
      $this->obj = $obj;
      $this->client = $client;

      $this->requirements();

      if(Core::getClient()->isAuthenticated($this->client)) $this->token = Core::getClient()->getToken($this->client);

      if($this->valid) $this->execute();
      else Core::getClient()->sendError($this->client, 6, "Execution cancelled (Action: '". $this->obj->action ."'). Not all requirements are met");
    }

    /*
    Functions which should be overwritten by the child class
    */
    public function requirements() {}
    public function execute() {}

    /*
      Parameter Management
    */
    public final function list(): array { return $this->params; }

    public function paramValue($key) {
      if(array_key_exists($key, $this->params)) return $this->params[$key];
      else return null;
    }

    public function paramCheck($param, $required = true): bool {
      $value = (isset($this->obj->$param) ? $this->obj->$param : "");

      if($value == "" && $required) {
        $this->error($this->client, "Parameter '". $param ."' required but not given", 6);

        $this->valid = false;

        return false;
      } else if($value != "") $this->params[$param] = $value;

      return true;
    }

  }
