<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FormController extends Controller
{
    public function show($id)
    {
        $default = App::make('default');
        $default->getById($id);
        $defaultOutput = $default->render();
        $configurations = $default->getConfigurations();
        return view('.show', compact('defaultOutput', 'configurations', 'id'));
    }

    public function save(Request $request, $dossierId)
    {
     
        $default = App::make('default');
        $default->getById($request->_id);
        $default->saveData($dossierId, $request->_id, $request->all());

        return redirect()->back()->with('success', ' data saved successfully.');
    }
}
