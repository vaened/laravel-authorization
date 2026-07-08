<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Models;

use Vaened\Authorization\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Vaened\Sentinel\Subject as SubjectContract;

class Subject extends Model implements SubjectContract
{
    use Authorizable;

    public    $timestamps = false;

    protected $guarded    = [];
}
