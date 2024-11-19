<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

use App\Models\ImportData;

class ImportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $faker = Faker::create();
        $batchSize = 500;
        $data = [];
        foreach (range(1, 100000) as $index) {
            $data[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'alternate_phone' => $faker->optional()->phoneNumber,
                'gender' => $faker->randomElement(['Male', 'Female', 'Other']),
                'dob' => $faker->date($format = 'Y-m-d', $max = '2005-12-31'),
                'reg_number' => strtoupper($faker->unique()->bothify('REG###??##')),
                'address' => $faker->address,
            ];

            if ($index % $batchSize === 0) {    
                ImportData::insert($data);
                $data = [];
            }
        }

        if (!empty($data)) {
            ImportData::insert($data);
        }
    }
}
