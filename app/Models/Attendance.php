<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'checkin_time',
        'checkout_time',
        'caller_id',
    ];

    public const ATTENDANCE_ACTION = [
        'CHECKIN' => 'checked in.',
        'CHECKOUT' => 'checked out.',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
  
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public function caller()
    {
        return $this->belongsTo(User::class);
    }
}
