<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reads extends Model
{
    protected $table="genome_reads";
    public $timestamps = false;
    protected $fillable = ['sample_id', "position","genome_reads"];

}
