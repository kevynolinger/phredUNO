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

  class JoinAction extends Action {

    public function requirements() {
      $this->paramCheck("name");
      $this->paramCheck("password", false);
    }

    public function execute() {
      if(Core::getUser()->getStatus($this->token) != 1) {
        Core::getClient()->sendError($this->client, 14);

        return;
      }

      $name = $this->paramValue("name");
      $password = $this->paramValue("password");

      $name = iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE", str_replace("/\s+/", "_", strtolower($name)));
      $password = ($password != "" ? hash("sha256", $password) : "");

      Core::getDB()->query("SELECT gameID, password FROM ". DBPREFIX ."game WHERE name = :name AND ended = :date ORDER BY gameID DESC LIMIT 1");
      Core::getDB()->bind(":name", $name);
      Core::getDB()->bind(":date", "0000-00-00 00:00:00");
      $result = Core::getDB()->single();

      if(Core::getDB()->rowCount() != 1) {
        Core::getClient()->sendError($this->client, 15);

        return;
      }

      Core::getGame()->player()->join($result["gameID"], $this->token, $this->client);
    }

  }
