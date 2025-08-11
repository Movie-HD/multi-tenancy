<?php

namespace App\Filament\Admin\Resources\OrganizacionResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Admin\Resources\OrganizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizacion extends EditRecord
{
    protected static string $resource = OrganizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
