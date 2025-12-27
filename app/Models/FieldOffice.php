<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldOffice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'is_headquarters'
    ];

    protected $appends = ['field_chiefs'];

    public function getFieldChiefsAttribute(){
        try {
            return User::query()->permission('field_office_chief_'.strtolower($this->name))->get();
        }catch (\Exception $E){
            return [];
        }

    }
}
