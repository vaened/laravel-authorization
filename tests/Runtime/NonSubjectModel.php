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

final class NonSubjectModel extends Model
{
    public    $timestamps = false;

    protected $table      = 'subjects';

    protected $guarded    = [];
}
