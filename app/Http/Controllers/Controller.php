<?php

namespace Bowhead\Http\Controllers;

use Bowhead\Traits;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 * @package Bowhead\Http\Controllers
 */
class Controller extends BaseController
{
    /** traits */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Traits\DataCcxt;
}
