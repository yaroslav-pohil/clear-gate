<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Database\Factories\TaskFactory;

/**
 * Task model representing a task in the system.
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $status One of pending, in_progress, completed
 * @property int $user_id
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property-read User $user
 * 
 * @method static TaskFactory factory($count = null, $state = [])
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Task status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::updating(function ($task) {
            if ($task->isDirty('status') && $task->status === self::STATUS_COMPLETED) {
                $task->completed_at = now();
            }
        });
    }
}
