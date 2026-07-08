<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Facades;

use Illuminate\Support\Facades\Facade;
use Vaened\Sentinel\Authorization\Authorizer as AuthorizerService;
use Vaened\Sentinel\Authorization\Junction;
use Vaened\Sentinel\Subject;

/**
 * @method static bool can(Subject $subject, array $permissions, Junction $junction = Junction::Or)
 * @method static bool cannot(Subject $subject, array $permissions, Junction $junction = Junction::Or)
 * @method static bool is(Subject $subject, array $roles, Junction $junction = Junction::Or)
 * @method static bool isnt(Subject $subject, array $roles, Junction $junction = Junction::Or)
 *
 * @see AuthorizerService
 */
final class Authorizer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AuthorizerService::class;
    }
}
