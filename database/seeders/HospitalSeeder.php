<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'Benadir Hospital',
                'address' => 'Wadada Maka Al-Mukarama, Mogadishu, Somalia',
                'phone' => '+252-61-5555001',
                'email' => 'info@benadirhospital.so',
                'status' => 'active',
            ],
            [
                'name' => 'Medina Hospital',
                'address' => 'Hodan District, Mogadishu, Somalia',
                'phone' => '+252-61-5555002',
                'email' => 'contact@medinahospital.so',
                'status' => 'active',
            ],
            [
                'name' => 'De Martino Hospital',
                'address' => 'Hamar Weyne District, Mogadishu, Somalia',
                'phone' => '+252-61-5555003',
                'email' => 'info@demartinohospital.so',
                'status' => 'active',
            ],
            [
                'name' => 'Erdogan Hospital',
                'address' => 'Hodan District, Mogadishu, Somalia',
                'phone' => '+252-61-5555004',
                'email' => 'contact@erdoganhospital.so',
                'status' => 'active',
            ],
            [
                'name' => 'Keysaney Hospital',
                'address' => 'Wadajir District, Mogadishu, Somalia',
                'phone' => '+252-61-5555005',
                'email' => 'info@keysaneyhospital.so',
                'status' => 'active',
            ],
            [
                'name' => 'Somali Turkish Training and Research Hospital',
                'address' => 'Hodan District, Mogadishu, Somalia',
                'phone' => '+252-61-5555006',
                'email' => 'info@sttrh.so',
                'status' => 'active',
            ],
            [
                'name' => 'Hayat Hospital',
                'address' => 'Dharkenley District, Mogadishu, Somalia',
                'phone' => '+252-61-5555007',
                'email' => 'contact@hayathospital.so',
                'status' => 'active',
            ],
            [
                'name' => 'Aamin Ambulance Hospital',
                'address' => 'Hodan District, Mogadishu, Somalia',
                'phone' => '+252-61-5555008',
                'email' => 'info@aaminambulance.so',
                'status' => 'active',
            ],
        ];

        foreach ($hospitals as $hospitalData) {
            Hospital::create($hospitalData);
        }
    }
}

