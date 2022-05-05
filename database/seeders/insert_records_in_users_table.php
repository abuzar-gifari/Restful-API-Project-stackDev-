<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class insert_records_in_users_table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users=[
            [
                "name"=>"Sk Md Abuzar Gifari",
                "email"=>"gifari@gmail.com",
                "password"=>bcrypt("12345"),
            ],
            [
                "name"=>"Saad Khan",
                "email"=>"saad@gmail.com",
                "password"=>bcrypt("12345"),
            ],
        ];
        User::insert($users);
    }
}
