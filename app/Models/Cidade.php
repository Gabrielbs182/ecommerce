<?php

namespace App\Models;

class Cidade extends RModel
{
    protected $table = "cidade";

    protected $fillable = ['nome','estado_id'];


}