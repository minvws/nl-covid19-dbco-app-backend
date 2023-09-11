<?php

declare(strict_types=1);

namespace Tests\Unit\Schema;

use App\Models\Purpose\Purpose;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\UnitTestCase;

use function count;
use function strlen;

#[Group('schema')]
#[Group('schema-purpose')]
class PurposeLabelTest extends UnitTestCase
{
    public function testIfAllCasesHaveLabelsAndIdentifiers(): void
    {
        $this->assertTrue(count(Purpose::cases()) > 0);
        foreach (Purpose::cases() as $p) {
            $this->assertTrue(strlen($p->getIdentifier()) > 0);
            $this->assertTrue(strlen($p->getLabel()) > 0);
        }
    }
}