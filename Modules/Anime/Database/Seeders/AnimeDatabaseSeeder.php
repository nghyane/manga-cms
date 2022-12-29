<?php

namespace Modules\Anime\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AnimeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        \Modules\Anime\Entities\Anime::factory(10)->create();

        // $this->call("OthersTableSeeder");
    }
}
