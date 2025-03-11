<?php 


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class LanguageController extends Controller
{
    public function switchLang($locale)
    {
        if (array_key_exists($locale, config('app.locales'))) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    }
}
