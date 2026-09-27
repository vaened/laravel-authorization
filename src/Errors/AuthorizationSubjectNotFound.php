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

use Vaened\Sentinel\Errors\AuthorizationError;

final class AuthorizationSubjectNotFound extends AuthorizationError
{
    public function __construct()
    {
        parent::__construct('Unable to resolve an authorization subject for the current request.');
    }
}
