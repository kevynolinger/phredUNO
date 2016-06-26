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

  class GetstatusAction extends Action {

    public function execute() {
      $status = Core::getUser()->getStatus($this->token);
      $gameID = 0;

      $response = array(
        "status" => $status
      );

      if($status == 2) {
        $gameID = Core::getUser()->getGame($this->token);

        if(!Core::getGame()->basic()->exists($gameID)) Core::getUser()->setStatus($this->token, 1);
        else $response["gameID"] = Core::getUser()->getGame($this->token);
      }

      Core::getClient()->sendSuccess($this->client, $response);
    }

  }
