<?php

namespace App\Models;

use App\utils\GuuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

    use HasFactory;

    public $incrementing = false;
    protected $keyType ='string';
    protected $fillable = [
        'user_id',
        'avatar',
        'lista_entradas',
        'is_deleted',
    ];

    protected $casts = [
        'lista_entradas' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($cliente){
            if (empty($cliente->id)){
                $cliente->id = GuuidGenerator::generateHash();
            }
        });
    }

    public function scopeFindByUserId($query, $userid)
    {
        return $query->where('user_id', $userid);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $term = strtolower($term);

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(dni) LIKE ?', ["%{$term}%"])
                ->orWhereHas('user', function ($q2) use ($term) {
                    $q2->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
                });
        });
    }


}
