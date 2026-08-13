<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;

class WhatsAppTemplateController extends Controller
{
    public function index()
    {
        $templates=WhatsappTemplate::latest('id')->paginate(30);
        return view('admin.whatsapp.templates',compact('templates'));
    }

    public function store(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|max:120',
            'meta_template_name'=>'required|max:150',
            'language_code'=>'required|max:20',
            'category'=>'required|max:50',
            'body'=>'nullable|string',
            'variables_json'=>'nullable|array',
            'status'=>'required|max:30',
            'is_active'=>'boolean',
        ]);
        $data['created_by']=auth()->id();
        $data['updated_by']=auth()->id();
        WhatsappTemplate::create($data);
        return back()->with('success','WhatsApp template saved.');
    }
}
