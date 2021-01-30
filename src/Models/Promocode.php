<?php

namespace Zorb\Promocodes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promocode extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'quantity', 'data', 'is_disposable', 'auth_required', 'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'data' => 'array',
        'is_disposable' => 'boolean',
        'auth_required' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('promocodes.database.promocodes_table'));
    }

    /**
     * Promocode and user connection.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $related = config('promocodes.database.user_model');
        $table = config('promocodes.database.pivot_table', 'promocode_user');
        $foreignPivotKey = config('promocodes.database.foreign_pivot_key', 'promocode_id');
        $relatedPivotKey = config('promocodes.database.related_pivot_key', 'user_id');

        return $this->belongsToMany($related, $table, $foreignPivotKey, $relatedPivotKey)
            ->using(PromocodeUser::class)
            ->withPivot('used_at');
    }

    /**
     * Scopes record by code.
     *
     * @param Builder $builder
     * @param string $code
     * @return Builder
     */
    public function scopeByCode(Builder $builder, string $code): Builder
    {
        return $builder->where('code', $code);
    }

    /**
     * Scopes expired records.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeExpired(Builder $builder): Builder
    {
        return $builder->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', Carbon::now());
    }

    /**
     * Scopes not expired records.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNotExpired(Builder $builder): Builder
    {
        return $builder->whereNull('expires_at')
            ->orWhereDate('expires_at', '>', Carbon::now());
    }

    /**
     * Scopes disposable records.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeDisposable(Builder $builder): Builder
    {
        return $builder->where('is_disposable', true);
    }

    /**
     * Scopes non-disposable records.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNonDisposable(Builder $builder): Builder
    {
        return $builder->where('is_disposable', false);
    }

    /**
     * Determine if the given model is available for use.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->hasQuantity() && !$this->isExpired();
    }

    /**
     * Determine if the given model has quantity left.
     *
     * @return bool
     */
    public function hasQuantity(): bool
    {
        if ($this->quantity === null) {
            return true;
        }

        return $this->quantity > 0;
    }

    /**
     * Determine if the given model is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return Carbon::now()->gte($this->expires_at);
    }

    /**
     * Determine if the given model is disposable.
     *
     * @return bool
     */
    public function isDisposable(): bool
    {
        return $this->is_disposable;
    }

    /**
     * Determine if the given model requires authentication.
     *
     * @return bool
     */
    public function authRequired(): bool
    {
        return $this->auth_required;
    }
}
