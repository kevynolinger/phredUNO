<?php

  /**
   * @author: Kevin Olinger, 2016-06-26
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-28
   */

  namespace phredUNO\action;
  use phredUNO\action\Action;
  use phredUNO\Core;

  class NewgameAction extends Action {

    public function requirements() {
      $this->paramCheck("name");
      $this->paramCheck("players");
      $this->paramCheck("cards");
      $this->paramCheck("password", false);
    }

    public function execute() {
      if(Core::getUser()->getStatus($this->token) != 1) {
        Core::getClient()->sendError($this->client, 14);

        return;
      }

      $gameID = Core::getGame()->management()->new($this->paramValue("name"), ($this->paramValue("password") != null ? $this->paramValue("password") : ""), $this->paramValue("players"), $this->paramValue("cards"), $this->token);

      if($gameID != 0) {
        Core::getClient()->sendSuccess($this->client, array(
          "message" => "New game successfully created.",
          "gameid" => $gameID
        ));

        Core::getGame()->player()->join($gameID, $this->token, $this->client);
      } else Core::getClient()->sendError($this->client, 18);
    }

  }
