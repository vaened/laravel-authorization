<?php

declare(strict_types=1);

/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Observers\GrantableObserver;

/**
 * Trait Grantable.
 *
 * @package Enea\Authorization\Traits
 *
 * @property int id
 * @property string secret_name
 */
trait Grantable
{
    use Model;

    public function getSecretName(): string
    {
        return $this->secret_name;
    }

    public function getIdentificationKey(): string
    {
        return (string) $this->getKey();
    }

    public static function bootGrantable(): void
    {
        static::observe(GrantableObserver::class);
    }
}
