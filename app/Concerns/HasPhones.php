<?php

namespace Modules\Auth\Concerns;

use Modules\Auth\Models\Model\Phone;

trait HasPhones
{
    public function getPhone()
    {
        return $this->phones;
    }

    public function setPhone($phone)
    {
        return $this->phones()->create([
            'number' => $phone,
        ]);
    }

    public function phones()
    {
        return $this->morphMany(Phone::class, 'model')->orderBy('created_at');

    }
}
