<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Visitor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "visitors";

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
        'name', 'email', 'tel', 'company_id', 'meeting_id', 'check_in', 'check_out'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the meetings the user hosts
     *
     * @return BelongsTo
     */
    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting');
    }

    /**
     * Get the visitor's company
     *
     * @return HasOne
     */
    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    /**
     * Get user through meeting
     *
     * @return HasOneThrough
     */
    public function user()
    {
        return $this->hasOneThrough(
            'App\Models\User',
            'App\Models\Meeting',
            'id',
            'id',
            'meeting_id',
            'user_id'
        );
    }

    /**
     * Get room through meeting
     *
     * @return HasOneThrough
     */
    public function room()
    {
        return $this->hasOneThrough(
            'App\Models\Room',
            'App\Models\Meeting',
            'id',
            'id',
            'meeting_id',
            'room_id'
        );
    }
}
