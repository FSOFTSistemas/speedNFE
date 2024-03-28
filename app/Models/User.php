<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cargo',
        'empresa_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function adminlte_image()
    {
        return asset('avatar/user.png');
    }

    public static function boot()
    {
        parent::boot();
        static::created(function ($user) {
            $permissao = [
                "master" => ['master'],
                "admin" => ['admin'],
                "client-NFe" => ['client-NFe'],
                "client-MDFe" => ['client-MDFe'],
                "cliente-advanced" => ['client-NFe', 'client-MDFe']
            ];
            $user->givePermissionTo($permissao[$user->cargo]);
        });
    }

}
