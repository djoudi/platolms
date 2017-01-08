<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'first', 'last', 'bio', 'img', 'question', 'answer', 'address', 'address_2', 'city', 'postal', 'state', 'country', 'timezone', 'phone', 'stripe_id', 'card_brand', 'card_last_four', 'trial_ends_at', 'ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ip', 'stripe_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'trial_ends_at', 'deleted_at'
    ];

    /**
     * Get the role of the user
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'roles_users');
    }
    
    /**
     * Get the highest role that a user has
     */
    public function getHighestRole()
    {
        return $this->belongsToMany('App\Models\Role', 'roles_users')->orderBy('id')->first();
    }

    /**
     * Get the roles that a user belongs to in a string
     */
    public function getRolesNames()
    {
        $returnedRoles = [];
        $roles = $this->belongsToMany('App\Models\Role', 'roles_users')->orderBy('id')->get();
        foreach ($roles as $role)
        {
            $returnedRoles[] = $role->name;
        }

        return implode(',', $returnedRoles);
    }

}
