<?php
/**
 * Created on 11/02/18 by enea dhack.
 */

namespace Enea\Authorization\Traits;

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
        return $this->getKey();
    }
}