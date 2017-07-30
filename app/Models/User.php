<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, RecordsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function slackIntegrations()
    {
        return $this->hasMany(SlackIntegration::class);
    }

    public function emailIntegrations()
    {
        return $this->hasMany(EmailIntegration::class);
    }
}
