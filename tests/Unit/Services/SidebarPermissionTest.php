<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\MenuService\AdminMenuItem;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('includes healthcare module permissions in the central registry', function () {
    $permissions = collect(app(PermissionService::class)->getAllPermissions())
        ->pluck('permissions')
        ->flatten()
        ->all();

    expect($permissions)->toContain(
        'permission.view',
        'hospital.view',
        'doctor.view',
        'medicine_order.view',
        'medicine.view',
        'supplier.view',
        'delivery_location.view',
        'lab_test_booking.view',
        'lab_test.view',
        'scan_imaging_booking.view',
        'scan_imaging_service.view',
        'provider.view',
        'expense.view',
        'expense_category.view',
        'refund.view',
        'report.view',
        'collection.view'
    );
});

it('keeps a parent sidebar item visible when a child is permitted', function () {
    Permission::create(['name' => 'medicine_order.view', 'guard_name' => 'web']);
    Permission::create(['name' => 'supplier.view', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->givePermissionTo('supplier.view');

    $this->actingAs($user);

    $child = (new AdminMenuItem())
        ->setLabel('Suppliers')
        ->setPermissions('supplier.view');

    $parent = (new AdminMenuItem())
        ->setLabel('Medicines')
        ->setPermissions('medicine_order.view')
        ->setChildren([$child]);

    expect($parent->userHasPermission())->toBeTrue();
});
