<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoomEquipment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "room_equipment";

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
        'room_id', 'equipment_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get room
     *
     * @return HasOne
     */
    public function room()
    {
        return $this->hasOne('App\Models\Room', 'id', 'room_id');
    }

    /**
     * Get equipment
     *
     * @return HasOne
     */
    public function equipment()
    {
        return $this->hasOne('App\Models\Equipment', 'id', 'equipment_id');
    }
}
