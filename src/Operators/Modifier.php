<?php
/**
 * Created on 13/02/18 by enea dhack.
 */

namespace Enea\Authorization\Operators;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Contracts\GrantableOwner;
use Enea\Authorization\Exceptions\GrantableIsNotValidModelException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

abstract class Modifier
{
    private $resolver;

    public function __construct(AuthorizationRepositoryResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    protected function resolveAuthorizationsRelation(GrantableOwner $repository, Grantable $grantable): BelongsToMany
    {
        return $this->resolver->resolve($repository, $grantable);
    }

    protected function castToModel(Grantable $grantable): Model
    {
        if (! $grantable instanceof Model) {
            throw GrantableIsNotValidModelException::make($grantable);
        }

        return $grantable;
    }
}
