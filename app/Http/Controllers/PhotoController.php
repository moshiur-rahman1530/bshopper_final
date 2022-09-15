<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\Models\Photo;
use App\Models\Image;
use App\Models\Filemanager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use File;

class PhotoController extends Controller
{
    function PhotoIndex(){

        return view('admin.Photos');
    }
  
  
    function PhotoDelete(Request  $request){
  
        $OldPhotoURL=$request->input('OldPhotoURL');
        $OldPhotoID=$request->input('id');
  
        $OldPhotoURLArray= explode("/", $OldPhotoURL);
        $OldPhotoName=end($OldPhotoURLArray);
        $DeletePhotoFile= Storage::delete('public/'.$OldPhotoName);
  
        $DeleteRow= Photo::where('id','=',$OldPhotoID)->delete();
        return  $DeleteRow;
    }
  
  
      function PhotoUpload(Request $request){
        $photoPath=  $request->file('photo')->store('public');
  
          $photoName=(explode('/',$photoPath))[1];
  
          $host=$_SERVER['HTTP_HOST'];
          $location="http://".$host."/storage/".$photoName;
  
        $result= Photo::insert(['location'=>$location]);
        return $result;
      }






    public function index()
    {
        return view('admin.photo');
        // return view('admin.Image');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
        'images' => 'required',
        'images.*' => 'mimes:jpg,png,jpeg,gif,svg'
        ]);
        if($request->TotalImages > 0)
        {
           for ($x = 0; $x < $request->TotalImages; $x++) 
           {
               if ($request->hasFile('images'.$x)) 
                {
                    $file      = $request->file('images'.$x);
                    $path = $file->store('public');

                    // $photoPath=  $request->file('photo')->store('public');
                    $photoName=(explode('/',$path))[1];
                    $name = $file->getClientOriginalName();
                    $host=$_SERVER['HTTP_HOST'];
                    $location="http://".$host."/storage/".$photoName;
                    $insert[$x]['name'] = $name;
                    $insert[$x]['path'] = $location;
                }
           }
           $result=Image::insert($insert);
            return $result;
        }
        else{
           return response()->json(["message" => "Please try again."]);
        }
    }    


    public function uploadGaleryImage(Request $request)
    {

        // dd($request->all());
        
        if($request->UpdateTotalFiles > 0)
        {
           for ($x = 0; $x < $request->UpdateTotalFiles; $x++) 
           {
               if ($request->hasFile('img'.$x)) 
                {
                    $categoryImage = $request->file('img'.$x);
                    $categoryImageSaveAsName = time().$x . Auth::id() . "-image." . $categoryImage->getClientOriginalExtension();
                    // dd($categoryImageSaveAsName);
                    $all_upload_path = 'images/';
                    
                    $all_image_url = $all_upload_path . $categoryImageSaveAsName;

                    $success = $categoryImage->move($all_upload_path, $categoryImageSaveAsName);

                    $allImagesUpload = Image::create(['name'=>$categoryImageSaveAsName, 'path'=>$all_image_url]);
                 }
           }
           if ($allImagesUpload==true) {
            return 1;
          } else {
            return 0;
          }
          
        }else{
            return 'Image is required';
        }
        
    }    


public function getImages()
{
   $result = Image::take(10)->get();
//    $result = DB::table('filemanager')->take(10)->get();
   return $result;
}

function getImageById(Request $request){
    $FirstID=$request->id;
    $LastID=$FirstID+10;
    return Image::where('id','>=',$FirstID)->where('id','<',$LastID)->get();
    // return DB::table('filemanager')->where('id','>=',$FirstID)->where('id','<',$LastID)->get();
}


public function deleteImage(Request $request)
    {
        $id = $request->id;
        $path = $request->path;
    
        // $pathArray= explode("/", $path);
        // $ImageName=end($pathArray);
        // $DeletePhotoFile= Storage::delete('public/'.$ImageName);

        global $old_image;
        $category = Image::where('id','=',$id)->first();
        $old_image = $category->path;
       
            if (File::exists($old_image)) { 
                unlink($old_image);
            }

        $DeleteRow= Image::where('id','=',$id)->delete();
        // $DeleteRow= Filemanager::where('id','=',$id)->delete();

        if ($DeleteRow==true) {
            return 1;
        }else{
            return 0;
        }
        // return  $DeleteRow;
    }


}
