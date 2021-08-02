<?php

    include_once __DIR__ . '/Game.php';

    $notAWinner = true;

    $game = new Game();

    $game->add_player( "Chet" );
    $game->add_player( "Pat" );
    $game->add_player( "Sue" );

    do {

        $game->roll( rand( 0, 5 ) + 1 );

        if ( rand( 0, 9 ) === 7 ) {
            $notAWinner = $game->was_answered_wrong();
        } else {
            $notAWinner = $game->was_correctly_answered();
        }

    } while ($notAWinner);
  
