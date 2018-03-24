<?php

declare(strict_types=1);

/**
 * @author enea dhack <me@enea.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enea\Authorization\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Repository
{
    abstract protected function contract(): string;

    public function delete(string $secretName): bool
    {
        return $this->query()->where('secret_name', $secretName)->delete() > 0;
    }

    public function createMultiple(array $structs): Collection
    {
        return $this->register($structs);
    }

    protected function register(array $authorizations): Collection
    {
        return $this->transform($authorizations)->map(function (array $attributes): Model {
            return $this->query()->create($attributes);
        });
    }

    private function transform(array $authorization): Collection
    {
        return collect($authorization)->map(function (Struct $struct) {
            return [
                'display_name' => $struct->getName(),
                'description' => $struct->getDescription(),
            ];
        });
    }

    private function query(): Builder
    {
        return $this->model()->newQuery();
    }

    private function model(): Model
    {
        return app()->make($this->contract());
    }
}
