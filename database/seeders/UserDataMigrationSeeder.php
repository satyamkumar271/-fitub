<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Trainer;
use App\Models\Gym;

class UserDataMigrationSeeder extends Seeder
{
    public function run()
    {

        // Customers
        $customers = User::where('user_type','customer')->get();

        foreach ($customers as $user) {

            Customer::create([
                'user_id' => $user->id,
                'age' => $user->age,
                'weight' => $user->weight,
                'height' => $user->height,
                'goal' => $user->goal,
                'city' => $user->customer_city,
                'state' => $user->customer_state
            ]);
        }


        // Trainers
        $trainers = User::where('user_type','trainer')->get();

        foreach ($trainers as $user) {

            Trainer::create([
                'user_id' => $user->id,
                'phone_number' => $user->trainer_phone_number,
                'city' => $user->trainer_city,
                'state' => $user->trainer_state,
                'website_url' => $user->trainer_website_url,
                'specialization' => $user->specialization,
                'experience' => $user->experience
            ]);
        }


        // Gyms
        $gyms = User::where('user_type','gymowner')->get();

        foreach ($gyms as $user) {

            Gym::create([
                'user_id' => $user->id,
                'gym_name' => $user->gym_name,
                'gym_phone_number' => $user->gym_phone_number,
                'gym_email' => $user->gym_email,
                'gym_website_url' => $user->gym_website_url,
                'address_city' => $user->address_city,
                'address_state' => $user->address_state,
                'address_pincode' => $user->address_pincode,
                'total_members' => $user->total_members
            ]);
        }

    }
}