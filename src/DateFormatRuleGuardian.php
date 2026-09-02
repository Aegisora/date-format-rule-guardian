<?php

namespace Aegisora\RuleGuardians\DateFormatRule;

use Aegisora\Guardian\Guardian;

class DateFormatRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }
}
