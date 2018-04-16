<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Models;

use Enea\Authorization\Contracts\Deniable;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * Class UserPermission.
 *
 * @package Enea\Authorization\Models
 *
 * @property bool denied
 */
class UserPermission extends MorphPivot implements Deniable
{
    protected $casts = [
        'denied' => 'bool'
    ];

    public function isDenied(): bool
    {
        return $this->denied;
    }
}
