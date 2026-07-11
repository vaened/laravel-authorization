<?php

declare(strict_types=1);

/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vaened\Authorization\Tests\Integration\Gate;

use Illuminate\Contracts\Auth\Access\Gate;
use Vaened\Authorization\LaravelAuthorizationServiceProvider;
use Vaened\Authorization\Tests\DatabaseTestCase;
use Vaened\Authorization\Tests\Runtime\TestSubject;

final class LaravelGateIntegrationTest extends DatabaseTestCase
{
    public function test_it_gives_sentinel_precedence_over_laravel_gate_definitions(): void
    {
        $this->registerGateIntegration('before');

        $subject    = $this->subject();
        $permission = $this->permission('documents.read', 'Read Documents');
        $gate       = $this->app->make(Gate::class);

        $gate->define('documents.read', static fn(): bool => true);

        self::assertFalse($gate->forUser($subject)->check('documents.read'));

        $subject->grant($permission);

        self::assertTrue($gate->forUser($subject)->check('documents.read'));
    }

    public function test_it_ignores_subjects_that_are_not_eloquent_models(): void
    {
        $this->registerGateIntegration('before');

        $gate = $this->app->make(Gate::class);

        $gate->define('documents.read', static fn(): bool => true);

        self::assertTrue($gate->forUser(new TestSubject(1))->check('documents.read'));
    }

    public function test_it_uses_sentinel_only_when_laravel_has_no_decision(): void
    {
        $this->registerGateIntegration('after');

        $subject    = $this->subject();
        $permission = $this->permission('documents.create', 'Create Documents');
        $gate       = $this->app->make(Gate::class);

        $gate->define('documents.read', static fn(): bool => true);
        $gate->define('documents.delete', static fn(): bool => false);

        self::assertTrue($gate->forUser($subject)->check('documents.read'));
        self::assertFalse($gate->forUser($subject)->check('documents.delete'));
        self::assertFalse($gate->forUser($subject)->check('documents.create'));

        $subject->grant($permission);

        self::assertTrue($gate->forUser($subject)->check('documents.create'));
    }

    public function test_it_leaves_laravel_gate_definitions_unmodified_by_default(): void
    {
        $subject = $this->subject();
        $gate    = $this->app->make(Gate::class);

        $gate->define('documents.read', static fn(): bool => true);

        self::assertTrue($gate->forUser($subject)->check('documents.read'));
    }

    protected function registerGateIntegration(string $strategy): void
    {
        $this->app['config']->set('authorization.gate', $strategy);

        new LaravelAuthorizationServiceProvider($this->app)->boot();
    }
}
