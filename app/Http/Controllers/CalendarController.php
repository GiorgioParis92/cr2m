<?php 
// app/Http/Controllers/CalendarController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    
    public function index()
    {
        $departments = DB::table('departement')->get()->map(function($department) {
            return (array) $department; // Convert stdClass to array
        })->toArray();
        return view('calendar',compact('departments'));
    }
}
