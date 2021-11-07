<?php

namespace Bowhead\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $exchange_id
 * @property string $exchange_pair
 */
class BhExchangePairs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['exchange_id', 'market_id', 'exchange_pair'];

}
