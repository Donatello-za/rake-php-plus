<?php declare(strict_types=1);

use DonatelloZa\RakePlus\RakePlus;
use PHPUnit\Framework\TestCase;

class lang_ar_AE_Test extends TestCase
{
    public static bool $mb_support = true;

    protected function setUp(): void
    {
        self::$mb_support = true;
    }

    public function testCommaInArabicPhrase()
    {
        $text = "يا أمجد، افتح الباب.";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(2, $phrases);
        $this->assertContains('يا أمجد', $phrases);
        $this->assertContains('افتح الباب', $phrases);
    }

    public function testFullStopInArabicPhrase()
    {
        $text = ".ذهب الفتى إلى الحديقة ليلعب مع أصدقائه";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(2, $phrases);
        $this->assertContains('ذهب الفتى إلى الحديقة ليلعب', $phrases);
        $this->assertContains('أصدقائه', $phrases);
    }

    public function testQuotationsInArabicPhrase()
    {
        // Note that this test uses mixed (") marks, i.e. a quotation that starts
        // with Unicode character %U201C and ends with a standard ASCII (")
        $text = "“.قال عماد لأخيه : \"لا تنس أنني سأكون دائمًا معك، فلا داعي للقلق";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(3, $phrases);
        $this->assertContains('عماد لأخيه', $phrases);
        $this->assertContains('تنس أنني سأكون دائمًا معك', $phrases);
    }

    public function testRoundBracketsInArabicPhrase()
    {
        $text = ".الظروف الطبيعية القاسية (البرد الشديد ثم الجفاف) أفسدت موسم الفواكه هذا العام";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(5, $phrases);
        $this->assertContains('الظروف الطبيعية القاسية', $phrases);
        $this->assertContains('البرد الشديد', $phrases);
        $this->assertContains('الجفاف', $phrases);
        $this->assertContains('أفسدت موسم الفواكه', $phrases);
        $this->assertContains('العام', $phrases);
    }

    public function testColonInArabicPhrase()
    {
        $text = "“.قال عماد لأخيه : \"لا تنس أنني سأكون دائمًا معك، فلا داعي للقلق";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(3, $phrases);
        $this->assertContains('عماد لأخيه', $phrases);
        $this->assertContains('تنس أنني سأكون دائمًا معك', $phrases);
        $this->assertContains('فلا داعي للقلق', $phrases);
    }

    public function testDashesAndQuestionMarkInArabicPhrase()
    {
        $text = "هل أعدت لندى ساعتها التي نسيتها؟-

بالطبع، أعدتها لها بالأمس-

ممتاز-";

        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        // $this->assertContains('', $phrases);
        $this->assertCount(6, $phrases);
        $this->assertContains('هل أعدت لندى ساعتها', $phrases);
        $this->assertContains('نسيتها', $phrases);
        $this->assertContains('بالطبع', $phrases);
        $this->assertContains('أعدتها', $phrases);
        $this->assertContains('بالأمس', $phrases);
        $this->assertContains('ممتاز', $phrases);
    }

    public function testExclamationMarkInArabicPhrase()
    {
        $text = "“.قال عماد لأخيه : \"لا تنس أنني سأكون دائمًا معك، فلا داعي للقلق";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(3, $phrases);
        $this->assertContains('عماد لأخيه', $phrases);
        $this->assertContains('تنس أنني سأكون دائمًا معك', $phrases);
        $this->assertContains('فلا داعي للقلق', $phrases);
    }

    public function testSemicolonInArabicPhrase()
    {
        $text = "اجتهد الطالب في مذاكرته، فكان الأول على رفاقه.";
        $phrases = RakePlus::create($text, 'ar_AE', 0, false)->get();

        $this->assertCount(4, $phrases);
        $this->assertContains('اجتهد الطالب', $phrases);
        $this->assertContains('مذاكرته', $phrases);
        $this->assertContains('فكان الأول', $phrases);
        $this->assertContains('رفاقه', $phrases);
    }
}
