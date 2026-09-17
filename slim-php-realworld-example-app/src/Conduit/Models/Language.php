<?php
namespace Conduit\Models;
use Illuminate\Database\Eloquent\Model;
class Language extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'user_id'];
}
