<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $fillable = [
        'isi_posting',
        'lokasi_posting',
        'tanggal_posting',
    ]; 
}
