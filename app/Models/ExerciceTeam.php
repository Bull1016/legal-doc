<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExerciceTeam extends Model
{
    use HasFactory;

    protected $table = 'exercice_teams';

    protected $fillable = [
        'exercice_id',
        'member_id',
        'role_id',
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

    public function exercice() {
        return $this->belongsTo(Exercice::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }
}
