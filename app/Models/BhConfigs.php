<?php

namespace Bowhead\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $item
 * @property string $value
 */
class BhConfigs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['item', 'value', 'exchange_id'];

}
