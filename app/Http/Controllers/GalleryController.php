<?php

namespace App\Http\Controllers;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Auth;

class GalleryController extends Controller
{
    
    public function galleryCreate(){
        return view('galleries.galleryCreate');
    }

    //save the gallery
    public function galleryStore(Request $request){
         $this->validate($request,[
            'title'=>'required',
            'cover'=>'required',
            'description'=>'required'
        ]);
        $gallery= new Gallery;
        $gallery->title=$request->title;
        $gallery->description=$request->description; 
        $gallery->user_id=Auth::user()->id;
        
        $cover=$request->file('cover');
        $cover_ext=$cover->getClientOriginalExtension();
        $cover_name=rand(123456,999999) .'.'. $cover_ext;
        $cover_path=public_path('galleries/');
        $cover->move($cover_path,$cover_name);
        $gallery->cover=$cover_name;
        $gallery->save();
        return redirect()->route('home');
    }
    //show spacific photo
    public function galleryShow($id){
        $gallery=Gallery::find($id);
        return view('galleries.galleryShow',compact('gallery'));
    }
    //gallery edit
    public function galleryEdit($id){
        $gallery=Gallery::find($id);
        return view('galleries.galleryEdit',compact('gallery'));
    }
    //update in database
    public function galleryUpdate(Request $request,$id){
        $gallery=Gallery::find($id);
        $gallery->title=$request->title;
        $gallery->description=$request->description;
        $gallery_cover=$gallery->cover;
        if($request->hasFile('cover')){
            unlink(public_path('galleries/'. $gallery_cover));
            $cover=$request->file('cover');
            $cover_ext=$cover->getClientOriginalExtension();
            $cover_name=rand(123456,999999) .'.'. $cover_ext;
            $cover_path=public_path('galleries/');
            $cover->move($cover_path,$cover_name);
            $gallery->cover=$cover_name;
        }
        else{
            $gallery->cover=$request->old_cover;
        }
        $gallery->save();
       // return redirect()->route('galleryShow',$id);
       return redirect()->route('home');
    }
    //delete gallery
    public function galleryDelete($id){
        $gallery=Gallery::find($id);
        $gallery_cover=$gallery->cover;
        unlink(public_path('galleries/'. $gallery_cover));
        $gallery->delete();
        return redirect()->route('home');
    }
}
