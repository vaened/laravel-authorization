<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Blade;

use Illuminate\Support\Facades\Blade;

class Compiler
{
    public function make(): void
    {
        foreach ($this->directives() as $directive) {
            $this->addDirective($directive);
        }
    }

    protected function directives(): array
    {
        return [
            new IsDirective(),
            new CanDirective(),
            new IsntDirective(),
            new CannotDirective(),
        ];
    }

    private function addDirective(CheckableDirective $directive): void
    {
        Blade::if ($directive->name(), function (string $grantable, ?string $guard = null) use ($directive): bool {
            return $directive->isAuthorized($grantable, $guard);
        });
    }
}
