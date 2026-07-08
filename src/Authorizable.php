<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization;

use Vaened\Sentinel\Authorization;
use Vaened\Sentinel\Permission;
use Vaened\Sentinel\Subject;

interface Authorizable extends Subject
{
    public function can(string ...$permissions): bool;

    public function cannot(string ...$permissions): bool;

    public function grant(Authorization ...$authorizations): void;

    public function deny(Permission ...$permissions): void;

    public function revoke(Authorization ...$authorizations): void;

    public function actsAs(string ...$roles): bool;

    public function actsNotAs(string ...$roles): bool;
}
