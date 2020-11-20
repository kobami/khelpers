<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Laravel User model modified to work with WordPress.
 */
class User extends Authenticatable
{
    use Notifiable;

    // adjustments to work with WordPress
    protected $table = 'wp_users';
    protected $primary_key = 'ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_login",
        "user_nicename",
        "user_email",
        "user_url",
        "user_registered",
        "user_status",
        "display_name",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_pass', 'user_activation_key',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_registered' => 'datetime',
    ];
}
