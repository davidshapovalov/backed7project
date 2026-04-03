<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Note extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'notes';

    protected $primaryKey = 'id';

    //public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];



    public function publish(): bool
    {
        $this->status = 'published';
        return $this->save();
    }

    public function archive(): bool
    {
        $this->status = 'archived';
        return $this->save();
    }

    public static function searchPublished(string $q, int $limit = 20)
    {
        $q = trim($q);

        return static::query()
            ->where('status', 'published')
            ->where(function (Builder $x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                    ->orWhere('body', 'like', "%{$q}%");
            })
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    public function pin(): bool
    {
        $this->is_pinned = true;
        return $this->save();
    }

    public function unpin(): bool
    {
        $this->is_pinned = false;
        return $this->save();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'note_category');
    }

    public static function archiveOldDrafts(): int
    {
        return static::query()
            ->where('status', 'draft')
            ->where('updated_at', '<', now()->subDays(30))
            ->update([
                'status' => 'archived',
            ]);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'note_id', 'id');
    }
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
