<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('كلمة المرور')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
                Select::make('roles')
                    ->label('الأدوار')
                    ->relationship(
                        'roles',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => auth()->user()?->hasRole('Super Admin')
                            ? $query
                            : $query->where('name', '!=', 'Super Admin'),
                    )
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->saveRelationshipsUsing(function (Select $component, Model $record, ?array $state): void {
                        $roleIds = collect($state ?? [])
                            ->map(fn ($roleId): string => (string) $roleId)
                            ->all();

                        $component->getRelationship()->sync(static::filterAssignableRoleIds($record, $roleIds));
                    }),
            ]);
    }

    /**
     * @param  array<int, string>  $roleIds
     * @return array<int, string>
     */
    public static function filterAssignableRoleIds(Model $record, array $roleIds): array
    {
        if (auth()->user()?->hasRole('Super Admin')) {
            return array_values(array_unique($roleIds));
        }

        $superAdminRole = Role::query()
            ->where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first();

        if (! $superAdminRole) {
            return array_values(array_unique($roleIds));
        }

        $superAdminRoleId = (string) $superAdminRole->getKey();

        $roleIds = array_values(array_diff($roleIds, [$superAdminRoleId]));

        if ($record instanceof User && $record->hasRole('Super Admin')) {
            $roleIds[] = $superAdminRoleId;
        }

        return array_values(array_unique($roleIds));
    }
}
