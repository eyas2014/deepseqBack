<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reads extends Model
{
    protected $table="genome_reads";
    public $timestamps = false;
    protected $fillable = ['sample_id', "genome_position","genome_reads"];

}
