<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relationships
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return [
            'active' => 'success',
            'inactive' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        return [
            'active' => 'ใช้งาน',
            'inactive' => 'ไม่ใช้งาน'
        ][$this->status] ?? 'ไม่ระบุ';
    }

    // Model Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function activate()
    {
        $this->status = 'active';
        return $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        return $this->save();
    }
}