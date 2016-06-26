<?php

  /**
   * @author: Kevin Olinger, 2016-06-25
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-25
   */

  namespace phredUNO\action;
  use phredUNO\action\Action;
  use phredUNO\Core;

  class AuthenticateAction extends Action {

    public function requirements() {
      $this->paramCheck("token");
      $this->paramCheck("device", false);
    }

    public function execute() {
      if(Core::getClient()->isAuthenticated($this->client)) {
        Core::getClient()->sendError($this->client, 12);

        return;
      }

      Core::getDB()->query("SELECT accountID, username, language FROM ". DBPREFIX ."account WHERE token = :token LIMIT 1");
      Core::getDB()->bind(":token", $this->paramValue("token"));
      $result = Core::getDB()->single();

      if(Core::getDB()->rowCount() != 1) {
        Core::getClient()->sendError($this->client, 13, "Token '". $this->paramValue("token") ."' does not exist or is invalid");

        return;
      }

      Core::getUser()->update($result["accountID"],  $this->paramValue("token"), $this->client, $result["username"]);

      Core::getDB()->query("INSERT INTO ". DBPREFIX ."token_usage (accountID, date, IP, device) VALUES (:accountID, :date, :IP, :device)");
      Core::getDB()->bind(":accountID", $result["accountID"]);
      Core::getDB()->bind(":date", date("Y-m-d-H-i-s"));
      Core::getDB()->bind(":IP", Core::getClient()->get($this->client)->remoteAddress);
      Core::getDB()->bind(":device", ($this->paramValue("device") != null ? $this->paramValue("device") : "Unknown"));
      Core::getDB()->execute();

      Core::getLog()->info("Token '". $this->paramValue("token") ."' has been used to authenticate ". Core::getUser()->getUsername($this->paramValue("token")), $this->client);

      Core::getClient()->sendSuccess($this->client, array(
        "message" => "Successfully authenticated!",
        "username" => $result["username"],
        "language" => $result["language"]
      ));
    }

  }
