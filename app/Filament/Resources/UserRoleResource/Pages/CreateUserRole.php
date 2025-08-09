<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserRoleResource\Pages;

use App\Filament\Resources\UserRoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRole extends CreateRecord
{
    protected static string $resource = UserRoleResource::class;
}
