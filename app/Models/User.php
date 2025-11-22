<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Forms\Components\TextInput;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected $fillable = [
        'name',
        'email',
        'role_id',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function maktab():BelongsTo
    {
        return $this->belongsTo(Maktab::class);
    }

    protected static function boot()
    {
        parent::boot();
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
                        ->label("Foydalanuvchining email")
                        ->email()
                        ->required(),
                    TextInput::make('password')
                        ->label("Foydalanuvchining paroli")
                        ->revealable(true)
                        ->password()
                        ->required(),
                    Select::make('maktab_id')
                        ->label("Foydalanuvchining maktabi")
                        ->relationship('maktab', 'name')
                        ->required(),
                    Select::make('role_id')
                        ->label("Foydalanuvchining roli")
                        ->relationship('role', 'name')
                        ->required(),
                ])
        ];
    }
}
