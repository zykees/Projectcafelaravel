<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];  

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

        public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Asia/Bangkok') : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Asia/Bangkok') : null;
    }
    public function getImageUrl(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/no-image.png');
    }

    public function getStatusColor(): string
    {
        return $this->status === self::STATUS_ACTIVE ? 'success' : 'danger';
    }
}