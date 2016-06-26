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

  class TaketurnAction extends Action {

    public function requirements() {
      $this->paramCheck("card");
    }

    public function execute() {
      if(Core::getUser()->getStatus($this->token) != 2) {
        Core::getClient()->sendError($this->client, 14);

        return;
      }

      $card = $this->paramValue("card");
      $gameID = Core::getUser()->getGame($this->token);
      $currentPlayer = Core::getGame()->getCurrentPlayerUsername($gameID);

      if(!Core::getUser()->getCard($this->token, $card)) {
        Core::getClient()->sendError($this->client, 19);

        return;
      }

      if($currentPlayer == null && $currentPlayer != Core::getUser()->getUsername($this->token)) {
        Core::getClient()->sendError($this->client, 20);

        return;
      }

      Core::getGame()->turn($gameID, $this->token, $this->client, $card);
    }

  }
