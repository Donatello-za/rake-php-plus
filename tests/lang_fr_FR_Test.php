<?php

namespace DonatelloZa\RakePlus;

use PHPUnit_Framework_TestCase;

class lang_fr_FR_Test extends PHPUnit_Framework_TestCase
{
    public static $mb_support = true;

    protected function setUp()
    {
        self::$mb_support = true;
    }

    public function testFrenchShorthand()
    {
        $text = "Pour l'Arabie saoudite, l'accueil du Dakar s'inscrit dans un plan visant à préparer l'après-pétrole.";
        $phrases = RakePlus::create($text, 'fr_FR', 0, false)->get();

        $this->assertCount(6, $phrases);

        $this->assertContains('l\'arabie saoudite', $phrases);
        $this->assertContains('l\'accueil', $phrases);
        $this->assertContains('dakar s\'inscrit', $phrases);
        $this->assertContains('plan visant', $phrases);
        $this->assertContains('préparer l\'', $phrases);
        $this->assertContains('-pétrole', $phrases);
    }
}
