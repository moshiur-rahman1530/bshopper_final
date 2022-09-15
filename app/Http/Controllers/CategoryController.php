<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use File;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
      $result = Category::all();
        return view('admin.Category',['category'=>$result]);
    }


      public function catbyproduct()
      {
        // $id =$request->input('id');
        // $categoryCount = Product::where('product_cat', $id)->count();
        // return $categoryCount;

        // $products = DB::table('products')
        // ->leftjoin('categories','categories.id','=','products.product_cat')
        // ->selectRaw('COUNT(*) as cat', 'products.*')
        // ->groupBy('products.id')
        // ->get();
      }

    public function store(Request $request)
    {
      // dd($request->all());
        $name = $request->input('catName');
        $desc = $request->input('catDes');
        // $img = $request->input('catImg');
        $status = $request->input('status');

        $categoryImage = $request->file('catImg');
        $categoryImageSaveAsName = time() . Auth::id() . "-category." . $categoryImage->getClientOriginalExtension();
        // dd($categoryImageSaveAsName);
        $upload_path = 'images/category_image/';
        $all_upload_path = 'images/';
        $category_image_url = $upload_path . $categoryImageSaveAsName;
        $all_image_url = $all_upload_path . $categoryImageSaveAsName;

         // Storage::disk('google')->put($categoryImageSaveAsName, $categoryImage);

         Storage::disk('backup_google')->putFileAs("",$request->file('catImg'), $categoryImageSaveAsName);
         Storage::disk('google')->putFileAs("",$request->file('catImg'), $categoryImageSaveAsName);
         
        // $request->file('catImg')->store($categoryImageSaveAsName,"google");

        // $success = $categoryImage->move($upload_path, $categoryImageSaveAsName);

       $backupDisk = Storage::disk('backup_google')->url($categoryImageSaveAsName);
        $url = Storage::disk('google')->url($categoryImageSaveAsName);
        $filename = Storage::disk('google')->getMetadata($categoryImageSaveAsName);
        $path = $filename['path'];
        // dd($filename,'path23=',$path);
        // dd($path);


        

        // $uploadedFile = copy($upload_path.$categoryImageSaveAsName, $all_upload_path.$categoryImageSaveAsName);

        $allImagesUpload = Image::create(['name'=>$categoryImageSaveAsName, 'path'=>$backupDisk]);


        // $result = Category::create(['cat_name'=>$name, 'cat_des'=>$desc, "cat_img"=>$category_image_url,'status'=>$status]);
        $result = Category::create(['cat_name'=>$name, 'cat_des'=>$desc, 'img_name'=>$path, "cat_img"=>$url,'status'=>$status]);
        if ($result==true) {
          return 1;
        } else {
          return 0;
        }

    }


// get allcategory

  public function allcategory()
  {
    $result = Category::all();
    return $result;
  }

  // details category
  public function categoryDetails(Request $req)
  {
    // dd($id);
    $id = $req->input('id');
    $data=json_encode(Category::where('id','=',$id)->get());

    // dd($data);
    
      return $data;
  }


  // update category
  public function updateCategory(Request $req)
  {
    // dd($req->file('img'));
    $id = $req->input('id');
    $name = $req->input('name');
    $description = $req->input('description');
    $status = $req->input('status');

      if($req->hasFile('img')){
        $categoryImage = $req->file('img');
        $categoryImageSaveAsName = time() . Auth::id() . "-category." . $categoryImage->getClientOriginalExtension();
        // dd($categoryImageSaveAsName);
        $upload_path = 'images/category_image/';
        $all_upload_path = 'images/';

        $category_image_url = $upload_path . $categoryImageSaveAsName;
        $all_image_url = $all_upload_path . $categoryImageSaveAsName;

        // $success = $categoryImage->move($upload_path, $categoryImageSaveAsName);
        // $uploadedFile = copy($upload_path.$categoryImageSaveAsName, $all_upload_path.$categoryImageSaveAsName);

        Storage::disk('backup_google')->putFileAs("",$req->file('img'), $categoryImageSaveAsName);
        Storage::disk('google')->putFileAs("",$req->file('img'), $categoryImageSaveAsName);
      
        $backupUpdateDisk = Storage::disk('backup_google')->url($categoryImageSaveAsName);
        $Updateurl = Storage::disk('google')->url($categoryImageSaveAsName);

        $updatefilename = Storage::disk('google')->getMetadata($categoryImageSaveAsName);
        $updatepath = $updatefilename['path'];



        $allImagesUpload = Image::create(['name'=>$categoryImageSaveAsName, 'path'=>$backupUpdateDisk]);

          global $old_image;
          $categorys = Category::where('id','=',$id)->first();
          $old_image = $categorys->cat_img;
          $img_name = $categorys->img_name;
          $url = Storage::disk('google')->url($img_name);
          if ($url) { 
            $delete = Storage::disk('google')->delete('1kEldWn4ij4tKwSGBO4juAJAO5QN10sO2/'.$img_name );
          }
        
              // if (File::exists($old_image)) { 
              //     unlink($old_image);
              // }
      }else{
        $findUbcat = Category::where('id','=',$id)->first();
        $Updateurl =$findUbcat->cat_img;
        $updatepath = $findUbcat->img_name;
      }
      

      $result= Category::where('id','=',$id)->update([
        'cat_name'=>$name,
        'cat_des'=>$description,
        'cat_img'=>$Updateurl,
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


// delete categoryDelete

    public function categoryDelete(Request $req)
    {
      $id = $req->input('id');

      global $img_name;
      global $old_image;
        $category = Category::where('id','=',$id)->first();
        $old_image = $category->cat_img;
        $img_name = $category->img_name;

        // $url = Storage::disk('google')->getMetadata($old_image);
        $url = Storage::disk('google')->url($img_name);
        // $link = Storage::disk('google')->url('1kEldWn4ij4tKwSGBO4juAJAO5QN10sO2/10POLjASr43YehX80THGz0azst3dGAKxX');
        // $link = Storage::disk('google')->url('16631766721-category.jpg');

      //  Storage::disk('google')->delete($old_image);
       
            // if (File::exists($old_image)) { 
            //     unlink($old_image);
            // }
           
            // dd($url);
            if ($url) { 
              $delete = Storage::disk('google')->delete('1kEldWn4ij4tKwSGBO4juAJAO5QN10sO2/'.$img_name );
            }

      $result = Category::where('id','=',$id)->delete();
      if ($result==true) {
        return 1;
      } else {
        return 0;
      }
    }

}
