<?php

namespace Modules\Auth\Concerns;

use Modules\Auth\Models\Model\Email;

trait HasEmail
{
    public function getEmail()
    {
        return $this->emails;
    }

    public function setEmail($email)
    {
        return $this->emails()->create([
            'address' => $email,
        ]);
    }

    public function emails()
    {
        return $this->morphMany(Email::class, 'model')->orderBy('created_at');

    }
}
