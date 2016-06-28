<?php

  /**
   * @author: Kevin Olinger, 2016-06-20
   * @copyright: 2016+ Kevin Olinger
   *
   * Last modified: 2016-06-28
   */

  namespace phredUNO\system\game;
  use phredUNO\Core;

  class Player {

    public function join($gameID, $token, $client) {
      if(Core::getGame()->basic()->exists($gameID)) {
        if(Core::getGame()->basic()->hasStarted($gameID)) {
          Core::getClient()->sendError($client, 3);

          return;
        }

        if(Core::getGame()->basic()->getUsedSlots($gameID) >= Core::getGame()->basic()->getSlots($gameID)) {
          Core::getClient()->sendError($client, 4);

          return;
        }

        Core::getGame()->basic()->addPlayer($gameID, $token);

        $players = array();
        $plys = Core::getGame()->basic()->getPlayers($gameID);

        foreach($plys as $ply) $players[Core::getUser()->getUsername($ply)] = Core::getUser()->getGravatarHash($ply);

        foreach($plys as $ply) {
          Core::getUser()->order($ply, array(
            "name" => Core::getGame()->basic()->getName($gameID),
            "slots" => Core::getGame()->basic()->getSlots($gameID),
            "cards" => Core::getGame()->basic()->getCardAmount($gameID),
            "creator" => Core::getUser()->getUsername(Core::getGame()->basic()->getCreator($gameID)),
            "players" => $players
          ), "updategamewaiting");
        }

        Core::getUser()->setGame($token, $gameID);

        if(Core::getGame()->basic()->getUsedSlots($gameID) >= Core::getGame()->basic()->getSlots($gameID)) Core::getGame()->management()->start($gameID);
      } else Core::getClient()->sendError($client, 3);
    }

    public function turn($gameID, $token, $client, $card) {
      if(Core::getGame()->basic()->exists($gameID)) {
        if(!Core::getGame()->basic()->hasStarted($gameID)) {
          Core::getClient()->sendError($client, 22, "Tried to play a turn in a not-yet-started game with the ID '". $gameID ."'");

          return;
        }

        if(!in_array($token, Core::getGame()->basic()->getPlayers($gameID))) {
          Core::getClient()->sendError($client, 23);

          return;
        }

        if(Core::getGame()->basic()->getCurrentPlayerUsername($gameID) != Core::getUser()->getUsername($token)) {
          Core::getClient()->sendError($client, 26);

          return;
        }

        $cardColor = str_replace(substr($card, -3, 3), "", $card);
        $cardNumber = substr($card, -3, 1);

        $currentCard = Core::getGame()->basic()->getCurrentCard($gameID);
        $currentCardColor = str_replace(substr($currentCard, -3, 3), "", $currentCard);
        $currentCardNumber = substr($currentCard, -3, 1);

        $check = false;
        $lastTurn = (Core::getGame()->basic()->getLastTurner($gameID) == $token ? true : false);

        Core::getLog()->debug(Core::getUser()->getUsername($token) .": New card color: ". $cardColor ." - Current card color: ". $currentCardColor ." ; New card number: ". $cardNumber ." - Current card number: ". $currentCardNumber);

        if(($cardColor == $currentCardColor || $cardNumber == $currentCardNumber) && !$lastTurn) $check = true;
        else if($cardNumber == $currentCardNumber && $lastTurn) $check = true;
        else $check = false;

        if($check) {
          Core::getClient()->sendSuccess($client, "Successsfully played a card");

          Core::getUser()->removeCard($token, $card);
          Core::getUser()->sendCards($token);

          Core::getGame()->basic()->setCurrentCard($gameID, $card);
          Core::getGame()->management()->sendCurrentCard($gameID);
        } else Core::getClient()->sendError($client, 24);

        Core::getGame()->basic()->setLastTurner($gameID, $token);
      } else Core::getClient()->sendError($client, 21);
    }

    public function uno($gameID, $token, $client) {
      if(Core::getUser()->getCardAmount($token) != 1) {
        Core::getClient()->sendError($client, 26);

        Core::getGame()->management()->getRandomCard($gameID, $token, true);
      } else Core::getUser()->setUno($token);

      Core::getGame()->basic()->setCurrentPlayer($gameID);
      Core::getGame()->management()->sendCurrentPlayer($gameID);
    }

    public function finish($gameID, $token, $client) {
      $check = true;

      if(Core::getUser()->getCardAmount($token) < 2 && !Core::getUser()->getUno($token)) {
        Core::getClient()->sendError($client, 25);

        Core::getGame()->management()->getRandomCard($gameID, $token, true);
        Core::getGame()->management()->getRandomCard($gameID, $token, true);
      } else if(Core::getUser()->getCardAmount($token) == 0) {
        $check = false;

        Core::getGame()->management()->stop($gameID, $token);
      }

      if($check) {
        Core::getGame()->basic()->setCurrentPlayer($gameID);
        Core::getGame()->basic()->setLastTurner($gameID, $token);

        Core::getGame()->management()->sendCurrentPlayer($gameID);

        Core::getUser()->removeUno($token);
      }

      Core::getLog()->debug(Core::getUser()->getUsername($token) ." finished its round");
    }

    public function leave($gameID, $token, $client) {
      if(Core::getGame()->basic()->exists($gameID)) {
        Core::getGame()->basic()->removePlayer($gameID, $token);

        Core::getUser()->setGame($token, 0);
        Core::getUser()->setStatus($token, 1);
        Core::getUser()->order($token, "Successfully left the current game", "leavegame");
      } else Core::getClient()->sendError($client, 17);
    }

  }
