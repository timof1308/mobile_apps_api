<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Room extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "rooms";

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
        'name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the meetings of the room
     *
     * @return BelongsToMany
     */
    public function meetings()
    {
        return $this->belongsToMany('App\Models\Meeting');
    }

    /**
     * Get equipment for room through room_equipment
     *
     * @return HasManyThrough
     */
    public function equipment()
    {
        return $this->hasManyThrough('App\Models\Equipment', 'App\Models\RoomEquipment');
    }
}
