<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

use Enea\Authorization\Contracts\Grantable as GrantableContract;

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
    /**
     * {@inheritdoc}
     */
    public function getSecretName(): string
    {
        return $this->secret_name;
    }

    public function getIdentificationKey(): string
    {
        return $this->getKey();
    }

    protected static function grantableBySecretName(string $secretName): ?GrantableContract
    {
        return static::query()->where('secret_name', $secretName)->first();
    }
}
