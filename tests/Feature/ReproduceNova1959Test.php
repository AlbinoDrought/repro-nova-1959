<?php

namespace Tests\Feature;

use App\Permission;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Tests\TestCase;

class ReproduceNova1959Test extends TestCase
{
    use RefreshDatabase;

    public function testAttach()
    {
        // setup

        /** @var User $user */
        $user = User::query()
            ->where('name', '=', 'test')
            ->firstOrFail();
        $this->actingAs($user);

        /** @var Permission $permission */
        $permission = Permission::query()
            ->where('name', '=', 'live happily ever after')
            ->firstOrFail();

        /** @var User $target */
        $target = User::query()
            ->where('name', '=', 'bar')
            ->firstOrFail();
        $target->permissions()->detach();
        $target = $target->fresh();

        $this->assertFalse(
            $target->permissions()->where('name', '=', 'live happily ever after')->exists(),
            'sanity check: $target should not start with "live happily ever after" permission'
        );

        // following README.md:

        // #7: attach "live happily ever after" to "bar" user
        $this->json('POST', "/nova-api/permissions/{$permission->id}/attach-morphed/users?editing=true&editMode=attach", [
            'users' => $target->id,
            'viaRelationship' => 'users',
        ])->assertSuccessful();
        $target = $target->fresh();

        $this->assertTrue(
            $target->permissions()->where('name', '=', 'live happily ever after')->exists(),
            '$target should have "live happily ever after" permission after attach'
        );

        /** @var ActionEvent $event */
        $event = ActionEvent::query()
            ->where('name', '=', 'Attach')
            ->firstOrFail();

        $this->assertEquals(
            Permission::class,
            $event->actionable_type,
            'ActionEvent actionable_type should be Permission'
        );
        $this->assertEquals(
            $permission->id,
            $event->actionable_id,
            'ActionEvent actionable_id should be $permission->id'
        );

        $this->assertEquals(
            User::class,
            $event->target_type,
            'ActionEvent target_type should be User'
        );
        // currently fails, this is laravel/nova-issues#1959
        $this->assertEquals(
            $target->id,
            $event->target_id,
            'ActionEvent target_id should be $target->id'
        );

        // same as above target_type/target_id but resolves morph relation
        $this->assertTrue(
            $target->is($event->target),
            'ActionEvent target should be $target'
        );
    }
}
