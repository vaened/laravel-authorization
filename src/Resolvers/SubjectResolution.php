<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Resolvers;

use Vaened\Sentinel\Subject;

final readonly class SubjectResolution
{
    private const FOUND = 'found';

    private const NOT_FOUND = 'not_found';

    private const UNAVAILABLE = 'unavailable';

    private function __construct(
        private string       $status,
        private Subject|null $subject,
    )
    {
    }

    public static function found(Subject $subject): self
    {
        return new self(self::FOUND, $subject);
    }

    public static function notFound(): self
    {
        return new self(self::NOT_FOUND, null);
    }

    public static function unavailable(): self
    {
        return new self(self::UNAVAILABLE, null);
    }

    public function subject(): Subject|null
    {
        return $this->subject;
    }

    public function isFound(): bool
    {
        return self::FOUND === $this->status;
    }

    public function isNotFound(): bool
    {
        return self::NOT_FOUND === $this->status;
    }

    public function isUnavailable(): bool
    {
        return self::UNAVAILABLE === $this->status;
    }

    public function isCacheable(): bool
    {
        return !$this->isUnavailable();
    }
}
