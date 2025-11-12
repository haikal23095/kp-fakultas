<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * PENTING: Beri tahu Laravel nama tabelnya adalah 'Roles' (huruf besar R)
     */
    protected $table = 'Roles';

    /**
     * PENTING: Beri tahu Laravel Primary Key-nya adalah 'Id_Role'
     */
    protected $primaryKey = 'Id_Role';

    /**
     * Matikan timestamps jika Anda tidak punya created_at/updated_at
     */
    public $timestamps = false;
}


