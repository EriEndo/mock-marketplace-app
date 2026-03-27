<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'status',
        'requested_clock_in',
        'requested_clock_out',
         'note',
        'admin_id',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'requested_clock_in_at' => 'datetime',
        'requested_clock_out_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function requestBreaks()
    {
        return $this->hasMany(RequestBreak::class);
    }

    public function getStatusLabelAttribute()
{
    return match ($this->status) {
        'pending' => '承認待ち',
        'approved' => '承認済み',
    };
}
}
