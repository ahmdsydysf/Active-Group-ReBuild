<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One row per settings key. Read and write through App\Support\Settings, never directly. */
class Setting extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }
}
