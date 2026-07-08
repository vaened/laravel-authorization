<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Errors;

use Illuminate\Database\Eloquent\Model;
use Vaened\Sentinel\Errors\AuthorizationError;

class UnsupportedSubject extends AuthorizationError
{
    public static function becauseItDoesNotExtendModel(string $type): self
    {
        return new self(
            "The subject [$type] must extend [" . Model::class . '] to be used by the Laravel adapter.'
        );
    }
}
