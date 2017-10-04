<?php

namespace App\Tests\Feedback;

use App\Feedback\Subject;
use PHPUnit\Framework\TestCase;

class SubjectTest extends TestCase
{
    public function testGetSubjects()
    {
        $this->assertSame([Subject::INVALID_PICTURE, Subject::OTHER], Subject::getSubjects());
    }

    public function testGetTitleBySubject()
    {
        $this->assertSame('', Subject::getTitleBySubject(''));
        $this->assertSame(Subject::TITLE_OTHER, Subject::getTitleBySubject(Subject::OTHER));
        $this->assertSame(Subject::TITLE_INVALID_PICTURE, Subject::getTitleBySubject(Subject::INVALID_PICTURE));
    }
}
