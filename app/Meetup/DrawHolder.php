<?php

namespace App\Meetup;

class DrawHolder
{
    /**
     * Hold a draw in order to select lucky participants to take the spots.
     *
     * @param array $participants Participants that will take place at the draw
     * @param int   $spots        Available spots that will be distributed among the participants
     *
     * @return array Lucky participants
     */
    public function draw($participants, $spots)
    {
        shuffle($participants);

        return array_slice($participants, 0, $spots);
    }
}
