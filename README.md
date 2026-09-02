# Aegisora Date Format Rule Guardian

[![Latest Version](https://img.shields.io/packagist/v/aegisora/date-format-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/date-format-rule-guardian)
[![Total Downloads](https://img.shields.io/packagist/dt/aegisora/date-format-rule-guardian?style=flat-square)](https://packagist.org/packages/aegisora/date-format-rule-guardian)
![Code Coverage Badge](./badge.svg)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHPStan Badge](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)

Date Format Rule Guardian provides a simple shortcut for ensuring a value is a string that matches a given date/time format using `aegisora/guardian` and `aegisora/date-format-rule`.

It is designed for cases where you want to quickly check whether a value conforms to a date format without manually creating validation pipelines.

This package is built on top of:

- [aegisora/guardian](https://github.com/Aegisora/guardian)
- [aegisora/date-format-rule](https://github.com/Aegisora/date-format-rule)

---

## ✨ Features
- 🔹 Simple shortcut API for `DateFormatRule`
- 🔹 Validates whether a value matches a given date/time format
- 🔹 Optional `DateTimeZone` support
- 🔹 Uses `aegisora/guardian` internally
- 🔹 Uses `aegisora/date-format-rule` internally
- 🔹 Supports custom validation exceptions
- 🔹 Fully compatible with the Aegisora ecosystem
- 🔹 Ready to use out of the box

---

## 📦 Installation

```shell
composer require aegisora/date-format-rule-guardian
```

---

## 🚀 Core Concept

This package wraps the common validation flow:

```php
$guardian->check($value, new DateFormatRule('Y-m-d'), new InvalidValueException());
```

into a dedicated shortcut class:

```php
$dateFormatRuleGuardian->checkWithoutDateTimezone($value, 'Y-m-d', new InvalidValueException());
```

Instead of manually creating `DateFormatRule` and passing it to `Guardian`, you can use `DateFormatRuleGuardian` directly.

---

## 🏗️ Basic Usage

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\DateFormatRule\DateFormatRuleGuardian;

$guardian = new Guardian();

$dateFormatRuleGuardian = new DateFormatRuleGuardian($guardian);

try {
    $dateFormatRuleGuardian->checkWithoutDateTimezone('2026-08-31', 'Y-m-d');
    // value matches the format
} catch (GuardianValidationException $exception) {
    // value does not match the format
}
```

---

## 🌍 Usage with Time Zone

You may provide a `DateTimeZone` that will be used while parsing the value.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\DateFormatRule\DateFormatRuleGuardian;
use DateTimeZone;

$guardian = new Guardian();

$dateFormatRuleGuardian = new DateFormatRuleGuardian($guardian);

$dateFormatRuleGuardian->checkWithDateTimezone(
    '2026-08-31 12:00:00',
    'Y-m-d H:i:s',
    new DateTimeZone('Europe/Moscow')
);
```

---

## 🧩 Usage with Custom Exception

You may provide your own exception for validation failure.

```php
use Aegisora\Guardian\Guardian;
use Aegisora\RuleGuardians\DateFormatRule\DateFormatRuleGuardian;
use App\Exceptions\InvalidValueException;

$guardian = new Guardian();

$dateFormatRuleGuardian = new DateFormatRuleGuardian($guardian);

$dateFormatRuleGuardian->checkWithoutDateTimezone('not-a-date', 'Y-m-d', new InvalidValueException());
```

If the value does not match the format, the provided exception will be thrown.

This is useful when validation errors should have domain-specific meaning.

---

## 🧪 Example in Application Service

```php
use Aegisora\RuleGuardians\DateFormatRule\DateFormatRuleGuardian;
use App\Exceptions\InvalidValueException;

final class BookingService
{
    private DateFormatRuleGuardian $dateFormatRuleGuardian;

    public function __construct(
        DateFormatRuleGuardian $dateFormatRuleGuardian
    ) {
        $this->dateFormatRuleGuardian = $dateFormatRuleGuardian;
    }

    /**
     * @param mixed $value
     */
    public function process($value): void
    {
        $this->dateFormatRuleGuardian->checkWithoutDateTimezone($value, 'Y-m-d', new InvalidValueException());

        // business logic for a value matching the format
    }
}
```

---

## 🚨 Exceptions

This package does not define its own exception types. All errors are raised by the underlying `aegisora/guardian` package.

Both exceptions extend the abstract base class
`Aegisora\Guardian\Exceptions\GuardianException`,
so you can catch every validation error with a single `catch`:

```php
use Aegisora\Guardian\Exceptions\GuardianException;

try {
    $dateFormatRuleGuardian->checkWithoutDateTimezone($value, 'Y-m-d');
} catch (GuardianException $exception) {
    // handles GuardianValidationException and GuardianExecutingRuleException
}
```

### `GuardianValidationException`

Thrown when validation fails and no custom exception is provided.

```php
use Aegisora\Guardian\Exceptions\GuardianValidationException;

try {
    $dateFormatRuleGuardian->checkWithoutDateTimezone('not-a-date', 'Y-m-d');
} catch (GuardianValidationException $exception) {
    echo $exception->getRuleCode(); // "date_format_rule"
}
```

### `GuardianExecutingRuleException`

Thrown when the underlying rule execution fails (for example, when the value is not a string or the format is empty).

`Aegisora\Guardian\Exceptions\GuardianExecutingRuleException`

---

## 🧩 API

### `DateFormatRuleGuardian::checkWithoutDateTimezone()`

```php
/**
 * @param mixed $value
 */
public function checkWithoutDateTimezone(
    $value,
    string $format,
    ?\Throwable $exception = null
): void
```

Parameters:
- `$value` *(mixed)* — value to validate; considered valid when it is a string matching `$format`
- `$format` *(string)* — the expected date/time format (PHP `date()` format)
- `$exception` *(?\Throwable, default `null`)* — optional custom exception thrown on validation failure

### `DateFormatRuleGuardian::checkWithDateTimezone()`

```php
/**
 * @param mixed $value
 */
public function checkWithDateTimezone(
    $value,
    string $format,
    \DateTimeZone $timeZone,
    ?\Throwable $exception = null
): void
```

Parameters:
- `$value` *(mixed)* — value to validate; considered valid when it is a string matching `$format`
- `$format` *(string)* — the expected date/time format (PHP `date()` format)
- `$timeZone` *(\DateTimeZone)* — the time zone used while parsing the value
- `$exception` *(?\Throwable, default `null`)* — optional custom exception thrown on validation failure

Both methods return `void`. They communicate results through exceptions only — they return nothing on success and throw on failure:
- `GuardianValidationException` — validation failed and no custom exception was provided
- `GuardianExecutingRuleException` — the underlying rule failed to execute
- the provided custom exception — validation failed and a custom exception was passed

Example:

```php
$dateFormatRuleGuardian->checkWithoutDateTimezone('2026-08-31', 'Y-m-d');
```

With time zone:

```php
$dateFormatRuleGuardian->checkWithDateTimezone('2026-08-31 12:00:00', 'Y-m-d H:i:s', new DateTimeZone('Europe/Moscow'));
```

With custom exception:

```php
$dateFormatRuleGuardian->checkWithoutDateTimezone('not-a-date', 'Y-m-d', new InvalidValueException());
```

---

## 🏛️ Architecture

This package is a small shortcut layer over the Aegisora validation pipeline.

Flow:
1. `DateFormatRuleGuardian::checkWithoutDateTimezone()` or `checkWithDateTimezone()` is called
2. `DateFormatRule` is created with the given format (and optional time zone)
3. `Guardian` executes the rule
4. If validation succeeds, execution continues normally
5. If validation fails, custom exception or `GuardianValidationException` is thrown
6. If rule execution fails, `GuardianExecutingRuleException` is thrown

Internal flow:

```text
Value → DateFormatRuleGuardian → Guardian → DateFormatRule → Result → Exception
```

---

## 🔗 Related Packages

- [aegisora/guardian](https://github.com/Aegisora/guardian) — validation execution orchestrator
- [aegisora/date-format-rule](https://github.com/Aegisora/date-format-rule) — rule-based date format validation
- [aegisora/rule-contract](https://github.com/Aegisora/rule-contract) — base rule contract and validation result architecture

---

## ⚖️ License

This package is open-source and licensed under the MIT License. See the LICENSE for details.

---

## 🌱 Contributing

Contributions are welcome and greatly appreciated!. See the CONTRIBUTING for details.

---

## 🌟 Support

If you find this project useful, please consider giving it a star on GitHub!

It helps the project grow and motivates further development.
