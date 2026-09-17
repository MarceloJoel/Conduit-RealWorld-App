<?php
namespace Conduit\Models;
use Illuminate\Database\Eloquent\Model;
class Skill extends Model {
   public $timestamps = false;
   protected $fillable = ['name', 'user_id'];
}
