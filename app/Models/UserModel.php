<?php

namespace App\Models;

use Doctrine\Common\Lexer\Token;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];

    public function token()
    {
        return $this->hasMany(Token::class);
    }
}
