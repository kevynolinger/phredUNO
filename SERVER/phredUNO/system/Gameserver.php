<?php

  /**
   * @author: Kevin Olinger, 2016-06-18
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-25
   */

  namespace phredUNO\system;
  use phredUNO\Core;

  use Ratchet\MessageComponentInterface;
  use Ratchet\ConnectionInterface;

  class Gameserver implements MessageComponentInterface {

    public function onOpen(ConnectionInterface $con) {
      Core::getClient()->create($con);
    }

    public function onMessage(ConnectionInterface $con, $msg) {
      $client = $con->resourceId;

      if(!Core::getUtils()->isJson($msg)) {
        Core::getClient()->sendError($client, 8);

        return;
      }

      $data = json_decode($msg);

      if(!isset($data->action)) {
        Core::getClient()->sendError($client, 9);

        return;
      }

      if($data->action == "broadcast" && isset($data->message)) {
        Core::getClient()->broadcast($client, $data->message);

        return;
      }

      if($data->action != "authenticate" && !Core::getClient()->isAuthenticated($client)) {
        Core::getClient()->sendError($client, 10);

        return;
      }

      $requested = ucFirst($data->action) ."Action";
      $requestedClass = "phredUNO\action\\". $requested;

      if(!file_exists("phredUNO/action/". $requested .".php")) {
        Core::getClient()->sendError($client, 11);

        return;
      }

      new $requestedClass($data, $client);
    }

    public function onClose(ConnectionInterface $con) {
      $client = $con->resourceId;

      Core::getLog()->info("Client disconnected", $client);
      Core::getClient()->delete($client);
    }

    public function onError(ConnectionInterface $con, \Exception $ex) {
      $client = $con->resourceId;

      Core::getClient()->sendError($client, 7, $ex->getMessage());
      Core::getClient()->drop($client);
    }

  }
