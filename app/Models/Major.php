<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'majors';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Get the minors that belong to a major
     */
    public function minors()
    {
        return $this->belongsToMany('App\Models\Minor', 'majors_minors');
    }
}
