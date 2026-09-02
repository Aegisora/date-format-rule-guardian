<?php

namespace Aegisora\RuleGuardians\DateFormatRule\Tests\Unit;

use Aegisora\Guardian\Exceptions\GuardianExecutingRuleException;
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\DateFormatRule\DateFormatRuleGuardian;
use DateTimeZone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

class DateFormatRuleGuardianTest extends TestCase
{
    private DateFormatRuleGuardian $guardian;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardian = new DateFormatRuleGuardian(
            new Guardian()
        );
    }

    /**
     * @dataProvider getSuccessfullyCheckWithoutDateTimezoneProvidedData
     * @param mixed $value
     */
    public function testSuccessfullyCheckWithoutDateTimezone(
        $value,
        string $format
    ): void {
        $this->expectNotToPerformAssertions();

        $this->guardian->checkWithoutDateTimezone($value, $format);
    }

    public static function getSuccessfullyCheckWithoutDateTimezoneProvidedData(): array
    {
        return [
            'iso date matches' => [
                'value' => '2026-08-31',
                'format' => 'Y-m-d',
            ],
            'iso datetime matches' => [
                'value' => '2026-08-31 23:59:59',
                'format' => 'Y-m-d H:i:s',
            ],
            'leap day matches' => [
                'value' => '2024-02-29',
                'format' => 'Y-m-d',
            ],
            'time only matches' => [
                'value' => '14:30',
                'format' => 'H:i',
            ],
            'day only matches' => [
                'value' => '31',
                'format' => 'd',
            ],
            'no leading zeros matches its format' => [
                'value' => '2026-8-3',
                'format' => 'Y-n-j',
            ],
            'textual month matches' => [
                'value' => '31 August 2026',
                'format' => 'd F Y',
            ],
            'consistent weekday matches' => [
                'value' => 'Monday, 31 August 2026',
                'format' => 'l, d F Y',
            ],
            'ordinal suffix matches' => [
                'value' => '31st August 2026',
                'format' => 'jS F Y',
            ],
            'am pm matches' => [
                'value' => '2:30 PM',
                'format' => 'g:i A',
            ],
            'escaped literal matches' => [
                'value' => '2026-08-31T10:20:30',
                'format' => 'Y-m-d\TH:i:s',
            ],
            'unix timestamp matches' => [
                'value' => '1756645200',
                'format' => 'U',
            ],
        ];
    }

    public function testSuccessfullyCheckWithDateTimezone(): void
    {
        $this->expectNotToPerformAssertions();

        $this->guardian->checkWithDateTimezone(
            '2026-08-31 12:00:00',
            'Y-m-d H:i:s',
            new DateTimeZone('Europe/Moscow')
        );
    }

    /**
     * @dataProvider getFailedCheckWithoutDateTimezoneProvidedData
     */
    public function testFailedCheckWithoutDateTimezone(
        string $value,
        string $format,
        ?Throwable $customRuleValidationException,
        string $expectedExceptionClassName
    ): void {
        $this->expectException($expectedExceptionClassName);

        $this->guardian->checkWithoutDateTimezone($value, $format, $customRuleValidationException);
    }

    public static function getFailedCheckWithoutDateTimezoneProvidedData(): array
    {
        return [
            'empty string does not match' => [
                'value' => '',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'non date garbage does not match' => [
                'value' => 'not-a-date',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'overflow february does not match' => [
                'value' => '2026-02-31',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'february 29th on non leap year does not match' => [
                'value' => '2026-02-29',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'month out of range does not match' => [
                'value' => '2026-13-01',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'hour out of range does not match' => [
                'value' => '2026-08-31 24:00:00',
                'format' => 'Y-m-d H:i:s',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'missing leading zero does not match' => [
                'value' => '2026-8-3',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'wrong separator does not match' => [
                'value' => '2026/08/31',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'trailing garbage does not match' => [
                'value' => '2026-08-31 extra',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'leading garbage does not match' => [
                'value' => 'x2026-08-31',
                'format' => 'Y-m-d',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'partial input does not match' => [
                'value' => '2026-08-31',
                'format' => 'Y-m-d H:i:s',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'inconsistent weekday does not match' => [
                'value' => 'Sunday, 31 August 2026',
                'format' => 'l, d F Y',
                'customRuleValidationException' => null,
                'expectedExceptionClassName' => GuardianValidationException::class,
            ],
            'invalid value with custom rule validation exception' => [
                'value' => 'not-a-date',
                'format' => 'Y-m-d',
                'customRuleValidationException' => new CustomRuleException(),
                'expectedExceptionClassName' => CustomRuleException::class,
            ],
        ];
    }

    public function testFailedCheckWithDateTimezone(): void
    {
        $this->expectException(GuardianValidationException::class);

        $this->guardian->checkWithDateTimezone(
            'not-a-date',
            'Y-m-d H:i:s',
            new DateTimeZone('Europe/Moscow')
        );
    }

    public function testFailedCheckWithDateTimezoneWithCustomException(): void
    {
        $this->expectException(CustomRuleException::class);

        $this->guardian->checkWithDateTimezone(
            'not-a-date',
            'Y-m-d H:i:s',
            new DateTimeZone('Europe/Moscow'),
            new CustomRuleException()
        );
    }

    public function testFailedCheckWithDefaultCustomException(): void
    {
        $this->expectException(GuardianValidationException::class);

        try {
            $this->guardian->checkWithoutDateTimezone('not-a-date', 'Y-m-d');
        } catch (GuardianValidationException $exception) {
            self::assertSame('date_format_rule', $exception->getRuleCode());
            throw $exception;
        }
    }

    /**
     * @dataProvider getInvalidContextProvidedData
     * @param mixed $value
     */
    public function testFailedCheckCauseInvalidContextThrowsGuardianExecutingRuleException(
        $value
    ): void {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->checkWithoutDateTimezone($value, 'Y-m-d');
    }

    public static function getInvalidContextProvidedData(): array
    {
        return [
            'value - true' => [
                'value' => true,
            ],
            'value - false' => [
                'value' => false,
            ],
            'value - zero integer' => [
                'value' => 0,
            ],
            'value - positive integer' => [
                'value' => 20260831,
            ],
            'value - negative integer' => [
                'value' => -1,
            ],
            'value - zero float' => [
                'value' => 0.0,
            ],
            'value - positive float' => [
                'value' => 2026.0831,
            ],
            'value - negative float' => [
                'value' => -0.01,
            ],
            'value - null' => [
                'value' => null,
            ],
            'value - not empty array' => [
                'value' => ['2026-08-31',],
            ],
            'value - empty array' => [
                'value' => [],
            ],
            'value - object' => [
                'value' => new stdClass(),
            ],
            'value - callable' => [
                'value' => static function () {
                },
            ],
        ];
    }

    public function testFailedCheckCauseEmptyFormatThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $this->guardian->checkWithoutDateTimezone('2026-08-31', '');
    }

    public function testFailedCheckWithoutDateTimezoneCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new DateFormatRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->checkWithoutDateTimezone('2026-08-31', 'Y-m-d');
    }

    public function testFailedCheckWithoutDateTimezoneCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new DateFormatRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->checkWithoutDateTimezone('2026-08-31', 'Y-m-d');
    }

    public function testFailedCheckWithDateTimezoneCauseGuardianThrowsGuardianExecutingRuleException(): void
    {
        $this->expectException(GuardianExecutingRuleException::class);

        $guardian = new DateFormatRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(GuardianExecutingRuleException::class)
        );

        $guardian->checkWithDateTimezone('2026-08-31 12:00:00', 'Y-m-d H:i:s', new DateTimeZone('Europe/Moscow'));
    }

    public function testFailedCheckWithDateTimezoneCauseGuardianThrowsNotExpectedException(): void
    {
        $this->expectException(Throwable::class);

        $guardian = new DateFormatRuleGuardian(
            $this->getGuardianThrowsExceptionOnCheck(Throwable::class)
        );

        $guardian->checkWithDateTimezone('2026-08-31 12:00:00', 'Y-m-d H:i:s', new DateTimeZone('Europe/Moscow'));
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianThrowsExceptionOnCheck(string $expectedExceptionClass): Guardian
    {
        $guardian = $this->getGuardianMock();

        $guardian
            ->expects(self::once())
            ->method('check')
            ->willThrowException($this->createMock($expectedExceptionClass));

        return $guardian;
    }

    /**
     * @return Guardian|MockObject
     */
    private function getGuardianMock(): Guardian
    {
        /** @var Guardian|MockObject $mock */
        $mock = $this->createMock(Guardian::class);

        return $mock;
    }
}
