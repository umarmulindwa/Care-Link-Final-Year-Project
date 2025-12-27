<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class GroupLeader extends Model
{
    use HasFactory;

    protected $connection = "communication_tree_connection";


}
