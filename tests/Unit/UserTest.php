<?php

namespace Tests\Unit;

use Tests\UseDatabaseTrait;
use Tests\BaseTestCase;
use App\Models\User;
use App\Models\Language;

class UserTest extends BaseTestCase
{
    use UseDatabaseTrait;

    /** @test */
    public function it_can_be_created()
    {
        $attributes = [
            'username'   => 'johndoe',
            'firstname'  => 'John',
            'lastname'   => 'Doe',
            'email'      => 'jhon@example.com',
            'language'   => Language::EN,
        ];

        User::where('username', $attributes['username'])->delete();

        $user = new User($attributes);
        $user->save();

        $this->assertNotNull($user->id);

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $user[$key]);
        }
    }

    /** @test */
    public function it_has_name()
    {
        $user = new User([
            'firstname' => 'John',
            'lastname' => 'Doe'
        ]);

        $this->assertEquals('John Doe', $user->name);
    }
}