<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OtherServicesListTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('other_services_list')->delete();
        
        \DB::table('other_services_list')->insert(array (
            0 => 
            array (
                'id' => 1,
                'service_name' => 'Photography',
                'image_url' => 'http://localhost/storage/images/system/photography.jpg',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'service_name' => 'Videography',
                'image_url' => 'http://localhost/storage/images/system/videography.jpg',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'service_name' => 'Designer',
                'image_url' => 'http://localhost/storage/images/system/designer.jpg',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'service_name' => 'Artist',
                'image_url' => 'http://localhost/storage/images/system/band.jpg',
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}