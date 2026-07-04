<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Traits;

use Vaened\Authorization\Observers\GrantableObserver;

/**
 * Trait Grantable.
 *
 * @package Vaened\Authorization\Traits
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int id
 * @property string secret_name
 */
trait Grantable
{
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
