<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class RdvController extends \App\Http\Controllers\Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
       
       $rdvs=DB::table('rdv');
dd($request->user_i);
        if(isset($request->user_id) && $request->user_id>0) {
            $rdvs=$rdvs->where('user_id',$request->user_id);
        }

       $rdvs=$rdvs->get();

        $data=$rdvs;

        return response()->json($data);
    }
}
