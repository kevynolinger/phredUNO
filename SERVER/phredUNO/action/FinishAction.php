<?php

  /**
   * @author: Kevin Olinger, 2016-06-25
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-28
   */

  namespace phredUNO\action;
  use phredUNO\action\Action;
  use phredUNO\Core;

  class FinishAction extends Action {

    public function execute() {
      if(Core::getUser()->getStatus($this->token) != 2) {
        Core::getClient()->sendError($this->client, 14);

        return;
      }

      Core::getGame()->player()->finish(Core::getUser()->getGame($this->token), $this->token, $this->client);
    }

  }
