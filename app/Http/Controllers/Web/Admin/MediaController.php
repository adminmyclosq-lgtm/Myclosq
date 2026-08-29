<?php
namespace App\Http\Controllers\Web\Admin;
use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class MediaController extends Controller {
    public function index() { $media=Media::latest('id')->paginate(30); return view('admin.media.index',compact('media')); }
    public function store(Request $request) {
        $data=$request->validate(['file'=>'required|file|mimes:jpg,jpeg,png,webp,svg|max:5120','alt_text'=>'nullable|max:255']);
        $file=$data['file'];
        $path=$file->store('media','public');
        $image=@getimagesize($file->getRealPath());
        Media::create([
            'uuid'=>(string)Str::uuid(),'file_name'=>$file->getClientOriginalName(),'storage_path'=>$path,
            'mime_type'=>$file->getMimeType(),'file_size'=>$file->getSize(),'width'=>$image[0]??null,'height'=>$image[1]??null,
            'alt_text'=>$data['alt_text']??null,'uploaded_by'=>auth()->id()
        ]);
        return back()->with('success','Media uploaded.');
    }
}
