<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class MyBriefController
{
    public function show(Request $request)
    {
        return redirect()->route('reset.home');
    }
}
