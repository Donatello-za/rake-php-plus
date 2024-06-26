<?php declare(strict_types=1);

use DonatelloZa\RakePlus\RakePlus;
use PHPUnit\Framework\TestCase;

class lang_sv_SE_Test extends TestCase
{
    public static bool $mb_support = true;

    protected function setUp(): void
    {
        self::$mb_support = true;
    }

    public function testFrenchShorthand()
    {
        $text = <<<LANG
Sverige är rikt på mytologiska varelser som har fascinerat generationer genom åren. 
Bland de mest kända är tomten, en liten och klok varelse som sägs skydda gårdar och hus om de behandlas väl. 
Näcken, en mystisk och farlig vattenande, lockar människor till sig med sitt magiska fiolspel. 
Troll, stora och ofta fula varelser, bor i skogar och berg och är kända för sin styrka och list. 
Skogsrået, en förförisk kvinnlig varelse, lever i skogarna och kan lura män till fördärv. 
Dessa mytiska varelser är en viktig del av svensk folklore och har bidragit till landets rika kulturarv.
LANG;
        $phrases = RakePlus::create($text, 'sv_SE', 0, false)->get();

        $this->assertCount(33, $phrases);

        $this->assertContains('mytologiska varelser', $phrases);
        $this->assertContains('sägs skydda gårdar', $phrases);
        $this->assertContains('förförisk kvinnlig varelse', $phrases);
        $this->assertContains('fördärv', $phrases);
        $this->assertContains('svensk folklore', $phrases);
        $this->assertContains('landets rika kulturarv', $phrases);
    }
}