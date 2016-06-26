<?php

  /**
   * @author: Kevin Olinger, 2016-06-21
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-25
   */

  namespace phredUNO\system;
  use phredUNO\Core;
  use Ratchet\ConnectionInterface;

  class Client {

    protected $clients = array();

    public function exists($client): bool {
      if(array_key_exists($client, $this->clients)) return true;
      else return false;
    }

    public function create(ConnectionInterface $con): bool {
      $client = $con->resourceId;

      if(!$this->exists($client)) {
        $this->clients[$client]["connection"] = $con;

        Core::getLog()->info("New client connected", $client);

        return true;
      } else return false;
    }

    public function delete($client) {
      if($this->isAuthenticated($client)) Core::getUser()->delete($this->getToken($client), $client);
      if($this->exists($client)) unset($this->clients[$client]);
    }

    public function drop($client) {
      $this->clients[$client]["connection"]->close();
      $this->delete($client);

      Core::getLog()->info("Connection dropped", $client);
    }

    public function isAuthenticated($client): bool {
      if($this->exists($client)) {
        if(isset($this->clients[$client]["token"])) return true;
        else return false;
      } else return false;
    }

    /* SET */
    public function setToken($client, $token): bool {
      if($this->exists($client)) {
        $this->clients[$client]["token"] = $token;

        return true;
      } else return false;
    }

    /* GET */
    public function get($client): ConnectionInterface {
      if($this->exists($client)) return $this->clients[$client]["connection"];
      else return null;
    }

    public function getToken($client): string {
      if($this->isAuthenticated($client)) return $this->clients[$client]["token"];
      else return null;
    }

    /*
     Message management
    */
    public function response($client, $response, $code = 0, $status = "error", $from = "general") {
      if(!$this->exists($client)) {
        Core::getLog()->error(2, $client);

        return;
      }

      $this->clients[$client]["connection"]->send(
        json_encode(
          array(
            "status" => $status,
            "from" => $from,
            "code" => $code,
            "response" => ($response != "" ? $response : (Core::getUtils()->error()->exists($code) ? Core::getUtils()->error()->get($code) : "Unknown error"));
          )
        )
      );

      if($status == "error") {
        if(!Core::getLog()->warn($code, $client)) Core::getLog()->customWarn($response ." (Code: ". $code .")", $client);
      }
    }

    public function sendError($client, $code, $response = "") {
      $this->response($client, $response, $code);
    }

    public function sendSuccess($client, $response) {
      $this->response($client, $response, 0, "success");
    }

    public function order($client, $data, $order) {
      $this->response($client, $data, 0, "order", $order);
    }

    public function broadcast($sender, $message) {
      Core::getLog()->info("A broadcast message has been sent: ". $message, $sender);

      foreach($this->clients as $client) $client["connection"]->send(
        json_encode(
          array(
            "status" => "success",
            "from" => "general",
            "message" => $message
          )
        )
      );
    }

  }
