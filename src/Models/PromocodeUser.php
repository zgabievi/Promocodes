<?php

namespace Zorb\Promocodes\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PromocodeUser extends Pivot
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'used_at' => 'datetime',
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

        $this->setTable(config('promocodes.database.pivot_table'));
    }
}
