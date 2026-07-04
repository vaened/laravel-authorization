<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

namespace Vaened\Authorization\Observers;

use Vaened\Authorization\Support\Determiner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GrantableObserver
{
    public function saving(Model $model): void
    {
        if (Determiner::transformSecretNameToKebabCase()) {
            $name = $model->getAttribute('display_name');
            $model->setAttribute('secret_name', Str::kebab($name));
        }
    }
}
