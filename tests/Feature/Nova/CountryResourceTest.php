<?php

declare(strict_types=1);

namespace Tipoff\Addresses\Tests\Feature\Nova;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tipoff\Addresses\Models\Country;
use Tipoff\Addresses\Tests\TestCase;
use Tipoff\Authorization\Models\User;

class CountryResourceTest extends TestCase
{
    use DatabaseTransactions;

    private const NOVA_ROUTE = 'nova-api/countries';

    /**
     * @dataProvider dataProviderForIndexByRole
     * @test
     */
    public function index_by_role(?string $role, bool $hasAccess, bool $canIndex)
    {
        Country::factory()->count(4)->create();
        $user = User::factory()->create();

        if ($role) {
            $user->assignRole($role);
        }
        $this->actingAs($user);

        $response = $this->getJson(self::NOVA_ROUTE)
            ->assertStatus($hasAccess ? 200 : 403);

        if ($hasAccess) {
            $this->assertCount($canIndex ? 5 : 0, $response->json('resources'));
        }
    }

    public function dataProviderForIndexByRole()
    {
        return [
            'Admin' => ['Admin', true, true],
            'Owner' => ['Owner', true, true],
            'Executive' => ['Executive', true, true],
            'Staff' => ['Staff', true, true],
            'Former Staff' => ['Former Staff', false, false],
            'Customer' => ['Customer', false, false],
            'No Role' => [null, false, false],
        ];
    }

    /**
     * @dataProvider dataProviderForShowByRole
     * @test
     */
    public function show_by_role(?string $role, bool $hasAccess, bool $canView)
    {
        $model = Country::factory()->create();

        $user = User::factory()->create();
        if ($role) {
            $user->assignRole($role);
        }
        $this->actingAs($user);

        $response = $this->getJson(self::NOVA_ROUTE . "/{$model->id}")
            ->assertStatus($hasAccess ? 200 : 403);

        if ($hasAccess && $canView) {
            $this->assertEquals($model->id, $response->json('resource.id.value'));
        }
    }

    public function dataProviderForShowByRole()
    {
        return [
            'Admin' => ['Admin', true, true],
            'Owner' => ['Owner', true, true],
            'Executive' => ['Executive', true, true],
            'Staff' => ['Staff', true, true],
            'Former Staff' => ['Former Staff', false, false],
            'Customer' => ['Customer', false, false],
            'No Role' => [null, false, false],
        ];
    }

    /**
     * @dataProvider dataProviderForDeleteByRole
     * @test
     */
    public function delete_by_role(?string $role, bool $hasAccess, bool $canDelete)
    {
        $model = Country::factory()->create();

        $user = User::factory()->create();
        if ($role) {
            $user->assignRole($role);
        }
        $this->actingAs($user);

        // Request never fails
        $this->deleteJson(self::NOVA_ROUTE . "?resources[]={$model->id}")
            ->assertStatus($hasAccess ? 200 : 403);

        // But deletion will only occur if user has permissions
        $this->assertDatabaseCount('countries', $canDelete ? 0 : 2);
    }

    public function dataProviderForDeleteByRole()
    {
        return [
            'Admin' => ['Admin', true, false],
            'Owner' => ['Owner', true, false],
            'Executive' => ['Executive', true, false],
            'Staff' => ['Staff', true, false],
            'Former Staff' => ['Former Staff', false, false],
            'Customer' => ['Customer', false, false],
            'No Role' => [null, false, false],
        ];
    }
}
