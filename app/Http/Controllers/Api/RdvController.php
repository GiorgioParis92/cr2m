<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class RdvController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        dd($request);
       $rdvs=DB::table('rdv');
       $rdvs=$rdvs->get();

        $data=$rdvs;

        return response()->json($data);
    }
}
