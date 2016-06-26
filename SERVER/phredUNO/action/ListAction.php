<?php

  /**
   * @author: Kevin Olinger, 2016-06-26
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-26
   */

  namespace phredUNO\action;
  use phredUNO\action\Action;
  use phredUNO\Core;

  class ListAction extends Action {

    public function requirements() {
      $this->paramCheck("name");
    }

    public function execute() {
      Core::getDB()->query("SELECT gameID, name, password, numPlayers, created FROM ". DBPREFIX ."game WHERE name LIKE :gameName AND started = :dateEmpty AND ended = :dateEmpty");
      Core::getDB()->bind(":gameName", "%". $this->paramValue("name") ."%");
      Core::getDB()->bind(":dateEmpty", "0000-00-00 00:00:00");
      $result = Core::getDB()->resultset();

      $games = array();

      foreach($result as $game) {
        $games[$game["gameID"]] = array();
        $games[$game["gameID"]]["gameID"] = $game["gameID"];
        $games[$game["gameID"]]["name"] = $game["name"];
        $games[$game["gameID"]]["password"] = $game["password"];
        $games[$game["gameID"]]["numPlayers"] = $game["numPlayers"];
        $games[$game["gameID"]]["created"] = $game["created"];
      }

      Core::getClient()->order($this->client, $games, "gamelist");
    }

  }
