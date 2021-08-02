<?php

function echo_ln( $string ) {
    echo "$string\n";
}

class Players
{
    private $players = [];
    private $current_player = 0;

    public function add_player( $player_name ): int
    {
        array_push( $this->players, $player_name );

        return $this->get_player_count();
    }

    public function get_player_count(): int
    {
        return count( $this->players );
    }

    public function get_current_player()
    {
        return $this->players[ $this->current_player ];
    }

    public function get_current_player_id(): int
    {
        return $this->current_player;
    }

    public function set_next_player()
    {
        if ( ( $this->current_player + 1 ) === count( $this->players ) ) {
            $this->current_player = 0;
        } else {
            $this->current_player++;
        }
    }
}

class Game
{
    private $is_getting_out_of_penalty_box;

    private $places = [0],
            $purses = [0],
            $in_penalty_box = [0];
            
    private $pop_questions = [],
            $science_questions = [],
            $sports_questions = [],
            $rock_questions = [];

    private $players;

    public function  __construct() {
        $this->players = new Players();

        for ( $i = 0; $i < 50; $i++ ) {
			array_push( $this->pop_questions, "Pop Question $i" );
			array_push( $this->science_questions, "Science Question $i" );
			array_push( $this->sports_questions, "Sports Question $i" );
			array_push( $this->rock_questions, "Rock Question $i" );
    	}
    }

    private function player_has_penalty( $player ): int
    {
        return $this->in_penalty_box[ $player ];
    }

	private function set_player_position( $roll )
    {
        $current_position = $this->places[ $this->players->get_current_player_id() ];

        if ( ( $current_position + $roll ) > 11 ) {
            $this->places[ $this->players->get_current_player_id() ] = ( ( $current_position + $roll ) - 12 );
        } else {
            $this->places[ $this->players->get_current_player_id() ] += $roll;
        }
    }

    private function get_player_position(): int
    {
        return $this->places[ $this->players->get_current_player_id() ];
    }

	private function ask_question() {
		if ( $this->get_current_category() === "Pop" )
			echo_ln( array_shift( $this->pop_questions ) );

		if ( $this->get_current_category() === "Science" )
			echo_ln( array_shift( $this->science_questions ) );
		
		if ( $this->get_current_category() === "Sports" )
			echo_ln( array_shift( $this->sports_questions ) );

		if ( $this->get_current_category() === "Rock" )
			echo_ln( array_shift( $this->rock_questions ) );
	}


	private function get_current_category(): string
    {
        $current_position = $this->get_player_position();

        switch( $current_position ) {
            case 0:
            case 4:
            case 8:
                return "Pop";

            case 1:
            case 5:
            case 9:
                return "Science";

            case 2:
            case 6:
            case 10:
                return "Sports";

            default:
                return "Rock";
        }
    }

    private function send_player_to_penalty_box()
    {
        $this->in_penalty_box[ $this->players->get_current_player_id() ] = true;
    }

    private function did_player_win(): bool
    {
        return ! ( $this->get_player_purse() === 6 );
    }

    private function add_to_player_purse()
    {
        $this->purses[ $this->players->get_current_player_id() ]++;
    }

    private function get_player_purse(): int
    {
        return $this->purses[ $this->players->get_current_player_id() ];
    }

    public function add_player( $player_name )
    {
        $player_id = $this->players->add_player( $player_name );

        $this->places[ $player_id ] = 0;
        $this->purses[ $player_id ] = 0;
        $this->in_penalty_box[ $player_id ] = false;
    }

    public function roll( $roll ) {
        $current_player = $this->players->get_current_player();

        echo_ln( "$current_player is the current player" );
        echo_ln( "They have rolled a $roll" );

        $has_penalty = $this->player_has_penalty( $this->players->get_current_player_id() );
        if ( $has_penalty ) {
            if ( ( $roll % 2 ) !== 0 ) {
                $this->is_getting_out_of_penalty_box = true;

                echo_ln( "$current_player is getting out of the penalty box" );
            } else {
                echo_ln( "$current_player is not getting out of the penalty box" );

                $this->is_getting_out_of_penalty_box = false;

                return;
            }
        }

        $this->set_player_position( $roll );
        $new_position = $this->get_player_position();

        echo_ln( "$current_player's new location is $new_position" );
        echo_ln( "The category is " . $this->get_current_category() );

        $this->ask_question();
    }

	public function was_correctly_answered(): bool
    {
        $player_has_penalty = $this->player_has_penalty( $this->players->get_current_player_id() );
        $current_player = $this->players->get_current_player();

		if ( $player_has_penalty && ! $this->is_getting_out_of_penalty_box ) {
            $this->players->set_next_player();
            return true;
		} else {
			echo_ln( "Answer was correct!!!!" );

			$this->add_to_player_purse();
            $player_gold_count = $this->get_player_purse();

            echo_ln( "$current_player now has $player_gold_count Gold Coins." );

            $winner = $this->did_player_win();
            $this->players->set_next_player();

			return $winner;
		}
	}

	public function was_answered_wrong(): bool
    {
        $current_player = $this->players->get_current_player();

		echo_ln( "Question was incorrectly answered" );
		echo_ln( "$current_player was sent to the penalty box" );

		$this->send_player_to_penalty_box();
		$this->players->set_next_player();

		return true;
	}
}
