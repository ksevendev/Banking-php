<?php declare(strict_types=1);

/*
 * This file is part of the Banking\Log package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Banking\Log;

class_alias(JsonSerializableDateTimeImmutable::class, 'Banking\Log\DateTimeImmutable');

// @phpstan-ignore-next-line
if (false) {
    /**
     * @deprecated Use \Banking\Log\JsonSerializableDateTimeImmutable instead.
     */
    class DateTimeImmutable extends JsonSerializableDateTimeImmutable
    {
    }
}
