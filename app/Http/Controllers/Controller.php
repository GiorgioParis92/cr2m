<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function view($view, $data = [], $mergeData = [])
    {
        $device = session('device');

        if ($device == 'mobile') {
            $view = 'mobile.' . $view;
        } else {
            $view = '' . $view;
        }
        return view($view, $data, $mergeData);
    }
}
