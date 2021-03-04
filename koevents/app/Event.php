<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
   protected $casts = [
       'items'=>'array'
   ];

   protected $dates = ['date'];

   protected $guarded = [];

   public function user(){
       return $this->belongTo('App\Models\User');
   }
   public function users(){
       return $this->belongsToMany('App\Models\User');
   }
  
}
