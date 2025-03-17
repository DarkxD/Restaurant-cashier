<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckboxController extends Controller
{
    public function saveState(Request $request)
    {
        Session::put('showTodayClosedClients', $request->showTodayClosedClients);
        Session::put('showOldClosedClients', $request->showOldClosedClients);
        return response()->json(['status' => 'success']);
    }

    public function loadState()
    {
        return response()->json([
            'showTodayClosedClients' => Session::get('showTodayClosedClients', true),
            'showOldClosedClients' => Session::get('showOldClosedClients', false),
        ]);
    }
}