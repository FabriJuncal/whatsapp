<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    // Atributos protegidos por laravel, donde este espera que el usuario haga el INSERT.
    // Cada vez que se creá un campo nuevo en la tabla "chat" se debe agregar en este array.
    // De lo contrario, se obtendrá el mensaje:
    // SQLSTATE[HY000]: General error: 1364 Field '[NOMBRE_CAMPO]' doesn't have a default value
    protected $fillable = [
        'name', 'image_url', 'is_group'
    ];

    public function messages()
    {
        // hasMany() => Método que hace la relación de "Uno a Muchos"
        //  -> Parametro => Modelo con el que se quiere relacionar
        return $this->hasMany(Message::class);
    }

    public function users()
    {
        // belongsToMany() => Método que hace la relación de "Muchos a Muchos"
        return $this->belongsToMany(User::class);
    }
}
