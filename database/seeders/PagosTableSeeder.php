<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PagosTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pagos')->delete();
        
        
        
    }
}