<?php

namespace Modules\Auth\Data;

use Spatie\LaravelData\Data;

class AuthData extends Data
{
    public function __construct(
        public AuthenticatedUserData $user,
    ) {}
}
