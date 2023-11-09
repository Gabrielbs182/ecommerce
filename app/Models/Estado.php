<?php

namespace App\Models;

class Estado extends RModel
{
    protected $table = "estado";
    protected $fillable = ['nome','sigla'];
}
