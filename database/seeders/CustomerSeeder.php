<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample customers with realistic data
        $customers = [
            [
                'name' => 'Ahmed Hassan Mohamed',
                'phone' => '61-234-5678',
                'country_code' => '+252',
                'address' => 'Hodan District, Mogadishu, Somalia',
                'status' => 'active',
            ],
            [
                'name' => 'Fatima Ali Omar',
                'phone' => '70-987-6543',
                'country_code' => '+252',
                'address' => 'Wadajir District, Mogadishu, Somalia',
                'status' => 'active',
            ],
            [
                'name' => 'Mohamed Abdi Yusuf',
                'phone' => '61-555-1234',
                'country_code' => '+252',
                'address' => 'Karaan District, Mogadishu, Somalia',
                'status' => 'active',
            ],
            [
                'name' => 'Amina Ibrahim Hassan',
                'phone' => '70-444-9876',
                'country_code' => '+252',
                'address' => 'Hamar Weyne District, Mogadishu, Somalia',
                'status' => 'active',
            ],
            [
                'name' => 'Omar Said Ahmed',
                'phone' => '61-777-2468',
                'country_code' => '+252',
                'address' => 'Shangani District, Mogadishu, Somalia',
                'status' => 'inactive',
            ],
            [
                'name' => 'Khadija Mohamed Ali',
                'phone' => '70-333-1357',
                'country_code' => '+252',
                'address' => 'Boondheere District, Mogadishu, Somalia',
                'status' => 'active',
            ],
            [
                'name' => 'Hassan Abdullahi Omar',
                'phone' => '61-888-9999',
                'country_code' => '+252',
                'address' => 'Daynile District, Mogadishu, Somalia',
                'status' => 'active',
            ],
            [
                'name' => 'Sahra Mohamud Hassan',
                'phone' => '70-111-2222',
                'country_code' => '+252',
                'address' => 'Kahda District, Mogadishu, Somalia',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        // Create additional random customers for testing
        Customer::factory()->count(25)->create();
    }
}
