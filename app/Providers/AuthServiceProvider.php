<?php

namespace App\Providers;

use App\Models\ActionLog;
use App\Models\Customer;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\ActionLogPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\PaymentTransactionPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Setting::class => SettingPolicy::class,
        ActionLog::class => ActionLogPolicy::class,
        Customer::class => CustomerPolicy::class,
        PaymentTransaction::class => PaymentTransactionPolicy::class,
        Supplier::class => SupplierPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
