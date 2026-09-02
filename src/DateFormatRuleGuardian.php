<?php

namespace Aegisora\RuleGuardians\DateFormatRule;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\Rules\DateFormatRule;
use Throwable;

class DateFormatRuleGuardian
{
    private Guardian $guardian;

    public function __construct(
        Guardian $guardian
    ) {
        $this->guardian = $guardian;
    }

    /**
     * @param mixed $value
     * @throws GuardianExecutingRuleException
     * @throws GuardianValidationException
     * @throws Throwable
     */
    public function checkWithoutDateTimezone(
        $value,
        string $format,
        ?Throwable $exception = null
    ): void {
        $this->guardian->check($value, new DateFormatRule($format), $exception);
    }
}
