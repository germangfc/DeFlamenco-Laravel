<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
        'isDeleted'
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
    public function scopeFindByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function getProfilePhotoUrl()
    {
        if ($this->hasRole('cliente')) {
            $cliente = Cliente::where('user_id', $this->id)->first();
            return $cliente ? asset('storage/app/public/images/' . $cliente->foto_dni) : 'https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg?t=st=1739915366~exp=1739918966~hmac=5d19f77be0966f655a43f5fee452798d19849968b456dce69f42cc7400194b9d&w=740';
        }

        if ($this->hasRole('empresa')) {
            $empresa = Empresa::where('usuario_id', $this->id)->first();
            return $empresa ? asset('storage/' . $empresa->imagen) : 'https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg?t=st=1739915366~exp=1739918966~hmac=5d19f77be0966f655a43f5fee452798d19849968b456dce69f42cc7400194b9d&w=740';
        }

        return 'https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg?t=st=1739915366~exp=1739918966~hmac=5d19f77be0966f655a43f5fee452798d19849968b456dce69f42cc7400194b9d&w=740';
    }

}
