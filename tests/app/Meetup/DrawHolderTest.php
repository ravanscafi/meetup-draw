<?php

namespace App\Meetup;

use TestCase;

class DrawHolderTest extends TestCase
{
    public function testShouldDraw()
    {
        // Set
        $holder = new DrawHolder;
        $participants = [
            ['participant 1' => 'john doe'],
            ['participant 2' => 'jane doe'],
            ['participant 3' => 'jack doe'],
            ['participant 4' => 'james doe'],
        ];
        $spots = 2;

        // Actions
        $result = $holder->draw($participants, $spots);

        // Assertions
        // Really naive assertions but good enough for now
        $this->assertCount($spots, $result);
        foreach ($result as $luckyGuy) {
            $this->assertContains($luckyGuy, $participants);
        }
    }
}
