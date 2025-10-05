#!/bin/bash

# Permission Verification Script
# This script verifies that Hospital and Doctor permissions are working correctly

echo "=========================================="
echo "Permission Verification Script"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}✗ Error: artisan file not found. Please run this script from the project root.${NC}"
    exit 1
fi

echo "1. Checking file permissions..."
CACHE_PERMS=$(stat -c "%a" storage/framework/cache 2>/dev/null || stat -f "%A" storage/framework/cache 2>/dev/null)
if [ "$CACHE_PERMS" = "775" ] || [ "$CACHE_PERMS" = "777" ]; then
    echo -e "${GREEN}✓ Cache directory permissions: $CACHE_PERMS${NC}"
else
    echo -e "${YELLOW}⚠ Cache directory permissions: $CACHE_PERMS (should be 775)${NC}"
    echo "  Run: sudo chmod -R 775 storage/framework/cache"
fi
echo ""

echo "2. Checking user groups..."
if groups | grep -q "www-data"; then
    echo -e "${GREEN}✓ User is in www-data group${NC}"
else
    echo -e "${YELLOW}⚠ User is not in www-data group${NC}"
    echo "  Run: sudo usermod -a -G www-data \$USER"
    echo "  Then log out and log back in"
fi
echo ""

echo "3. Checking permissions in database..."
php artisan tinker --execute="
\$hospitalPerms = \Spatie\Permission\Models\Permission::where('name', 'like', 'hospital.%')->count();
\$doctorPerms = \Spatie\Permission\Models\Permission::where('name', 'like', 'doctor.%')->count();
echo 'Hospital permissions: ' . \$hospitalPerms . PHP_EOL;
echo 'Doctor permissions: ' . \$doctorPerms . PHP_EOL;
"
echo ""

echo "4. Checking permission assignments..."
php artisan tinker --execute="
\$role = \Spatie\Permission\Models\Role::where('name', 'Superadmin')->first();
\$hospitalPerms = \$role->permissions->filter(function(\$p) { return str_contains(\$p->name, 'hospital'); })->count();
\$doctorPerms = \$role->permissions->filter(function(\$p) { return str_contains(\$p->name, 'doctor'); })->count();
echo 'Hospital permissions assigned to Superadmin: ' . \$hospitalPerms . PHP_EOL;
echo 'Doctor permissions assigned to Superadmin: ' . \$doctorPerms . PHP_EOL;
"
echo ""

echo "5. Checking permission cache..."
php artisan tinker --execute="
\$registrar = app(\Spatie\Permission\PermissionRegistrar::class);
\$permCount = \$registrar->getPermissions()->count();
echo 'Total permissions in cache: ' . \$permCount . PHP_EOL;
"
echo ""

echo "6. Testing user permissions..."
php artisan tinker --execute="
\$user = \App\Models\User::first();
\$hospitalView = \$user->can('hospital.view');
\$doctorView = \$user->can('doctor.view');

if (\$hospitalView && \$doctorView) {
    echo '✓ PASS: User can view hospitals and doctors' . PHP_EOL;
    exit(0);
} else {
    echo '✗ FAIL: User cannot view hospitals or doctors' . PHP_EOL;
    echo '  hospital.view: ' . (\$hospitalView ? 'YES' : 'NO') . PHP_EOL;
    echo '  doctor.view: ' . (\$doctorView ? 'YES' : 'NO') . PHP_EOL;
    exit(1);
}
"

RESULT=$?
echo ""

if [ $RESULT -eq 0 ]; then
    echo -e "${GREEN}=========================================="
    echo "✅ ALL CHECKS PASSED"
    echo "==========================================${NC}"
    echo ""
    echo "The Hospital and Doctor menus should appear in the sidebar."
    echo ""
    echo "If you still don't see them:"
    echo "1. Clear your browser cache (Ctrl+Shift+Delete)"
    echo "2. Log out and log back in"
    echo "3. Hard refresh the page (Ctrl+Shift+R)"
else
    echo -e "${RED}=========================================="
    echo "⚠️  SOME CHECKS FAILED"
    echo "==========================================${NC}"
    echo ""
    echo "To fix the issues, run:"
    echo ""
    echo "  php artisan permission:clear-cache --force"
    echo "  php artisan db:seed --class=HospitalPermissionSeeder"
    echo "  php artisan db:seed --class=DoctorPermissionSeeder"
    echo ""
    echo "Then run this script again to verify."
fi

echo ""

