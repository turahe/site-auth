<?php
/*
 * This source code is the proprietary and confidential information of
 * Nur Wachid. You may not disclose, copy, distribute,
 *  or use this code without the express written permission of
 * Nur Wachid.
 *
 * Copyright (c) 2023.
 *
 *
 */

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Address\Models\Address;
use Modules\Auth\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function (User $user) {
            $user->assignRole('user');
            $user->setSetting(['licence_key' => Str::random(64)]);
            $user->setSetting(config('site.user_settings'));
            $user->setEmail($user->email);
            $user->setPhone($user->phone);
            $user->setEmail(fake()->unique()->safeEmail());
            $user->setPhone(fake()->unique()->phoneNumber);
            $user->addresses()->save(Address::factory()->create([
                'label' => 'home',
                'model_id' => $user->id,
                'model_type' => get_class($user),
            ]));
        });

        $user = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
        ])
            ->each(function (User $user) {
                $user->assignRole('super-admin');
            });
    }
}
