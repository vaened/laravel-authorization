<?php

declare(strict_types=1);

/**
 * Created on 19/03/18 by enea dhack.
 */

namespace Enea\Authorization\Tests\Models;

use Enea\Authorization\Contracts\Grantable;
use Enea\Authorization\Tests\TestCase;

abstract class AuthorizationTestCase extends TestCase
{
    abstract protected function authorization(string $name): Grantable;

    public function test_you_can_find_a_authorization_by_name(): void
    {
        $authorization = $this->authorization('Authorization');
        $located = call_user_func([get_class($authorization), 'locateByName'], 'authorization');

        $this->assertSame($authorization->getSecretName(), $located->getSecretName());
        $this->assertSame((string) $authorization->getIdentificationKey(), $located->getIdentificationKey());
    }

    public function test_the_conversion_to_string_returns_the_name_of_the_authorization(): void
    {
        $authorization = $this->authorization('Authorization');
        $this->assertSame((string) $authorization, 'authorization');
    }

    public function test_the_secret_name_transforms_to_kebab_case(): void
    {
        $authorization = $this->authorization('Articles Creator');
        $this->assertSame($authorization->getSecretName(), 'articles-creator');
    }

    public function test_the_secret_name_is_not_transformed(): void
    {
        $this->app->make('config')->set('authorization.authorizations.transform-secret-name-to-kebab-case', false);
        $authorization = $this->authorization('Articles Creator');
        $this->assertSame($authorization->getSecretName(), 'Articles Creator');
    }
}
