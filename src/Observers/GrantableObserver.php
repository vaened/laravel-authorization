<?php
/**
 * Created on 22/03/18 by enea dhack.
 */

namespace Enea\Authorization\Observers;

use Enea\Authorization\Support\Determiner;
use Illuminate\Database\Eloquent\Model;

class GrantableObserver
{
    public function saving(Model $model): void
    {
        if (Determiner::transformSecretNameToKebabCase()) {
            $name = $model->getAttribute('display_name');
            $model->setAttribute('secret_name', kebab_case($name));
        }
    }
}
