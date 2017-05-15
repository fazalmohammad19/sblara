<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'name',
		'start_date',
		'end_date',
		'access_level',
		'contest_amount',
		'max_amount',
		'max_member',
		'is_active'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date', 
        'end_date'
    ];

    /**
     * Get the creator that owns the contest.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The users that belong to the contest.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contestUsers()
    {
        return $this->belongsToMany(User::class, 'contest_portfolios', 'contest_id', 'user_id')
                    ->withPivot('join_date', 'approved')
                    ->withTimestamps();
    }

    /**
     * Determine if the current contest has been joined.
     *
     * @return boolean
     */
    public function isJoined()
    {
        return !! $this->contestUsers()->wherePivot('user_id', auth()->id())->count();
    }
}