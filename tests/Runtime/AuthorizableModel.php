<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Runtime;

use Illuminate\Database\Eloquent\Model;
use Vaened\Authorization\Authorizable;
use Vaened\Authorization\Authorizations;

final class AuthorizableModel extends Model implements Authorizable
{
    use Authorizations;

    public    $timestamps = false;

    protected $table      = 'subjects';

    protected $guarded    = [];
}
