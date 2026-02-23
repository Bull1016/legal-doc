<?php

namespace App\Models;

use App\Models\Agenda;
use App\Models\Member;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'banner',
        'place',
        'description',
        'exercice_id',
        'datetime',
        'state',
        'organisation_id',
        'agenda_id'
    ];

    protected static function booted()
    {
        self::creating(function ($instance) {
            $instance->string_id = Str::uuid();
        });

        self::updating(function ($instance) {
            if (!$instance->string_id) {
                $instance->string_id = Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'string_id';
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function images()
    {
        return $this->hasMany(ActivityImage::class);
    }
}
