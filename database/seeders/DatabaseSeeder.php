<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tank_model')->insert([
            'modelname' => 'HC35',
            'manufacturer' => 'Worthington',
            'capacity' => '600',
            'number_of_inserts' => '10',
            'number_of_tubes' => '12',
            'number_of_samples' => '5',
        ]);

        DB::table('tank_model')->insert([
            'modelname' => 'HC38',
            'manufacturer' => 'Worthington',
            'capacity' => '600',
            'number_of_inserts' => '10',
            'number_of_tubes' => '12',
            'number_of_samples' => '5',
        ]);

        DB::table('roles')->insert([
            ['role_name' => 'Administrator'],
            ['role_name' => 'Laborfachkraft'],
            ['role_name' => 'Arzt'],
            ['role_name' => 'Sekretariat'],
            ['role_name' => 'Default'],
        ]);

        DB::table('material_types')->insert([
            ['type_of_material' => 'MK'],
            ['type_of_material' => 'TM'],
        ]);

        DB::table('users')->insert(
            [
                'name' => 'AdminNutzer',
                'email' => 'Platzhalter@Ueberschreiben.de',
                'password' => Hash::make('Norg2022'),
                'role' => DB::table('roles')->where('role_name', '=', 'Administrator')->value('role_name'),
            ]
        );
    }
}
