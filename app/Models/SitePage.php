<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePage extends Model 
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'site_pages';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'content',
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

}
