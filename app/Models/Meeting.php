<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Meeting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "meetings";

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'room_id', 'date'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the meeting's host (user)
     *
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * Get the meeting's room
     *
     * @return HasOne
     */
    public function room()
    {
        return $this->hasOne('App\Models\Room', 'id', 'room_id');
    }

    /**
     * Get the visitors of the meeting
     *
     * @return HasMany
     */
    public function visitors()
    {
        return $this->hasMany('App\Models\Visitor', 'meeting_id', 'id');
    }
}
