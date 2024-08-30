<?php

namespace Modules\Auth\Data;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $email,
        public string $first_name,
        public string $last_name,
        public string $username,
        public string $avatar,
        public ?\DateTimeInterface $email_verified_at,
        public ?\DateTimeInterface $phone_verified_at,
    ) {}
}
