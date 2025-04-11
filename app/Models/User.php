<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Forms\Components\Section;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'email',
        'is_admin',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }
    public static function get_form(): array
    {
        return [
            Section::make('Yangi foydalanuvchi yaratish')
                ->columns(2)
                ->icon('heroicon-s-user')
                ->description("Yangi foydalanuvchi yaratish uchun quyidagilarni to'ldiring")
                ->schema([
                    TextInput::make('name')
                        ->label("Foydalanuvchining IFSH")
                        ->columnSpanFull()
                        ->required(),
                    TextInput::make('email')
                        ->label("Foydalanuvchining Email")
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->label("Foydalanuvchining Paroli")
                        ->revealable(true)
                        ->password()
                        ->required(),
                    Toggle::make('is_admin')
                        ->label("Foydalanuvchining adminlik statusi")
                        ->onColor('success')
                        ->offColor('danger')
                        ->required(),
                ])
        ];
    }
}
