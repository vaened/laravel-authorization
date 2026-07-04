<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Repositories;

class Authorization
{
    private $name;

    private $description;

    public function __construct(string $name, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
    }

    public static function fake(string $name, ?string $description = null): Authorization
    {
        return new static($name, $description);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
