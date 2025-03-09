<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     *
     * @property string $name  Nombre del usuario.
     *
     * @property string $email  Email del usuario.
     *
     * @property string $password  Contraseña del usuario.
     *
     * @property string $tipo  Tipo del usuario.
     *
     * @property boolean $isDeleted  Indica si el usuario ha sido eliminado.
     */
    /** @use HasFactory<UserFactory> */
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
     * Coge el valor de la columna email_verified_at
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

    /**
     * Genera un identificador único para el usuario.
     *
     * @return void  Identificador único para el usuario.
     */
    public function scopeFindByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Genera un identificador único para el usuario.
     *
     * @return string  Identificador único para el usuario.
     */

    public function getProfilePhotoUrl()
    {
        if ($this->hasRole('cliente')) {
            $cliente = Cliente::where('user_id', $this->id)->first();
            return $cliente ? asset('storage/images/' . $cliente->avatar) : 'https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg?t=st=1739915366~exp=1739918966~hmac=5d19f77be0966f655a43f5fee452798d19849968b456dce69f42cc7400194b9d&w=740';
        }

        if ($this->hasRole('empresa')) {
            $empresa = Empresa::where('usuario_id', $this->id)->first();
            return $empresa ? asset('storage/empresas/' . $empresa->imagen) : 'https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg?t=st=1739915366~exp=1739918966~hmac=5d19f77be0966f655a43f5fee452798d19849968b456dce69f42cc7400194b9d&w=740';
        }

        return 'https://img.freepik.com/free-vector/contact-icon-3d-vector-illustration-blue-button-with-user-profile-symbol-networking-sites-apps-cartoon-style-isolated-white-background-online-communication-digital-marketing-concept_778687-1715.jpg?t=st=1739915366~exp=1739918966~hmac=5d19f77be0966f655a43f5fee452798d19849968b456dce69f42cc7400194b9d&w=740';
    }


    /**
     * Relacion uno a uno con un cliente.
     *
     * @return mixed Cliente asociado al usuario.
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class);  // Suponiendo que un usuario tiene un solo cliente
    }


    /**
     * Relacion uno a uno con una empresa.
     *
     * @return mixed Empresa asociada al usuario.
     */
    public function empresa()
    {
        return $this->hasOne(Empresa::class);  // Suponiendo que un usuario tiene un solo cliente
    }



    /**
     * Se identifica al usuario
     *
     * @return mixed Cliente asociado al usuario.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    /**
     * Reclama un identificador único para el usuario.
     *
     * @return mixed Cliente asociado al usuario.
     */
    public function getJWTCustomClaims()
    {
        return [
            'id'=> $this->id,
        ];
    }
}
