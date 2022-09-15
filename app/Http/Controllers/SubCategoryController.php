<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use File;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
  public function index()
  {

    $category = Category::all();
      return view('admin.subCategory',['category'=>$category]);
  }


  public function store(Request $request)
  {

    // dd($request->all());
      $name = $request->input('name');
      $desc = $request->input('description');
      $cat = $request->input('cat_id');
      // $img = $request->input('img');
      $status = $request->input('status');


      $subCategoryImage = $request->file('img');
      $subCategoryImageSaveAsName = time() . Auth::id() . "-subcategory." . $subCategoryImage->getClientOriginalExtension();
      // dd($subCategoryImageSaveAsName);
      $upload_path = 'images/subcategory_image/';
      $all_upload_path = 'images/';
      $subCategory_image_url = $upload_path . $subCategoryImageSaveAsName;
      $all_image_url = $all_upload_path . $subCategoryImageSaveAsName;
      // $success = $subCategoryImage->move($upload_path, $subCategoryImageSaveAsName);
      // $uploadedFile = copy($upload_path.$subCategoryImageSaveAsName, $all_upload_path.$subCategoryImageSaveAsName);


      
     Storage::disk('backup_google')->putFileAs("",$request->file('img'), $subCategoryImageSaveAsName);
     Storage::disk('google')->putFileAs("",$request->file('img'), $subCategoryImageSaveAsName);

     $backupDisk = Storage::disk('backup_google')->url($subCategoryImageSaveAsName);
     $url = Storage::disk('google')->url($subCategoryImageSaveAsName);

     $filename = Storage::disk('google')->getMetadata($subCategoryImageSaveAsName);
     $path = $filename['path'];


      $allImagesUpload = Image::create(['name'=>$subCategoryImageSaveAsName, 'path'=>$backupDisk]);

      $result = SubCategory::insert(['name'=>$name, 'description'=>$desc,'cat_id'=>$cat, "img"=>$url, 'img_name'=>$path,'status'=>$status]);

      // $allImagesUpload = Image::create(['name'=>$subCategoryImageSaveAsName, 'path'=>$all_image_url]);
      // $result = SubCategory::insert(['name'=>$name, 'description'=>$desc,'cat_id'=>$cat, "img"=>$subCategory_image_url,'status'=>$status]);

      if ($result==true) {
        return 1;
      } else {
        return 0;
      }

  }

  // details SubCatTableBod
  public function subCatDetails(Request $req)
  {
    $id = $req->input('id');
    $data=json_encode(SubCategory::where('id','=',$id)->get());
      return $data;

  }

  // update sub category
  public function updateSubCat(Request $req)
  {
    // dd($req->all());
    $id = $req->input('id');

    $name = $req->input('name');
    $description = $req->input('description');
    $catId = $req->input('catId');
    // $img = $req->input('img');
    $status = $req->input('status');

    if($req->hasFile('img')){
      $subcategoryImage = $req->file('img');
      $subcategoryImageSaveAsName = time() . Auth::id() . "-subcategory." . $subcategoryImage->getClientOriginalExtension();
      // dd($subcategoryImageSaveAsName);
      $upload_path = 'images/subcategory_image/';
      $all_upload_path = 'images/';

      $subcategory_image_url = $upload_path . $subcategoryImageSaveAsName;
      $all_image_url = $all_upload_path . $subcategoryImageSaveAsName;

      // $success = $subcategoryImage->move($upload_path, $subcategoryImageSaveAsName);
      // $uploadedFile = copy($upload_path.$subcategoryImageSaveAsName, $all_upload_path.$subcategoryImageSaveAsName);

        Storage::disk('backup_google')->putFileAs("",$req->file('img'), $subcategoryImageSaveAsName);
        Storage::disk('google')->putFileAs("",$req->file('img'), $subcategoryImageSaveAsName);
      
        $backupUpdateDisk = Storage::disk('backup_google')->url($subcategoryImageSaveAsName);
        $Updateurl = Storage::disk('google')->url($subcategoryImageSaveAsName);

        $updatefilename = Storage::disk('google')->getMetadata($subcategoryImageSaveAsName);
        $updatepath = $updatefilename['path'];


      $allImagesUpload = Image::create(['name'=>$subcategoryImageSaveAsName, 'path'=>$backupUpdateDisk]);
      // $allImagesUpload = Image::create(['name'=>$subcategoryImageSaveAsName, 'path'=>$all_image_url]);

        global $old_image;
        $subcategorys = SubCategory::where('id','=',$id)->first();
        $old_image = $subcategorys->img;

        $img_name = $subcategorys->img_name;
          $url = Storage::disk('google')->url($img_name);
          if ($url) { 
            $delete = Storage::disk('google')->delete('1kEldWn4ij4tKwSGBO4juAJAO5QN10sO2/'.$img_name );
          }
        
            // if (File::exists($old_image)) { 
            //     unlink($old_image);
            // }
    }else{
        $findUbcat = SubCategory::where('id','=',$id)->first();
        $Updateurl =$findUbcat->img;
        $updatepath = $findUbcat->img_name;
        // $subcategory_image_url =$findUbcat->img;
    }
    
    $result= SubCategory::where('id','=',$id)->update([
     	'name'=>$name,
     	'description'=>$description,
     	'cat_id'=>$catId,
     	'img'=>$Updateurl,
     	'img_name'=>$updatepath,
     	'status'=>$status,
     ]);

     if($result==true){
       return 1;
     }
     else{
      return 0;
     }
  }


  // change sub cat Status

  public function subcategoryStatus(Request $req){
      $id = $req->input('id');
      // $getCategory = Category::find($id);
      $data = SubCategory::where('id',$id)->first();
      if ($data->status == 1) {
        $status = 0;
      } else {
        $status = 1;
      }
      $result = SubCategory::where('id',$id)->update(['status'=>$status]);
          if ($result==true) {
            return 1;
          } else {
            return 0;
          }
  }


// get allcategory

public function allsubcategory()
{
$result = SubCategory::all();
return $result;
}
// delete sub categoryDelete

  public function subcategoryDelete(Request $req)
  {
    $id = $req->input('id');

    global $old_image;
        $subcategory = SubCategory::where('id','=',$id)->first();
        $old_image = $subcategory->img;
        $img_name = $subcategory->img_name;

            $url = Storage::disk('google')->url($img_name);
            if ($url) { 
              $delete = Storage::disk('google')->delete('1kEldWn4ij4tKwSGBO4juAJAO5QN10sO2/'.$img_name );
            }
       
            // if (File::exists($old_image)) { 
            //     unlink($old_image);
            // }


    $result = SubCategory::where('id','=',$id)->delete();
    if ($result==true) {
      return 1;
    } else {
      return 0;
    }
  }

}
