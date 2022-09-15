<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {

      $size = Size::all();
        return view('admin.Size',['size'=>$size]);
    }



    // public function store(Request $request)
    // {
    //     $name = explode(",",$request->input('name'));
    //     $size = json_encode($name);
    //     $result = Size::insert(['name'=>$size]);
    //     if ($result==true) {
    //       return 1;
    //     } else {
    //       return 0;
    //     }
    // }

    // public function store(Request $request)
    // {
    //    $size = json_decode($request->input('name'));
    //    $name = explode(",",$size);
    //
    //     $sizes = json_encode($name);
    //         $result = Size::insert(['name'=>$sizes]);
    //         if ($result==true) {
    //           return 1;
    //         } else {
    //           return 0;
    //         }
    //
    // }


        // public function store(Request $request)
        // {
        //     foreach ($request->moreFields as $key => $value) {
        //       // check if exist or not
        //       $checkData =   Size::where('name',$value)->get();
        //       $dataPush=[];
        //       if ($checkData) {
        //         array_push($dataPush,$value->id);
        //       }else{
        //       array_push($dataPush,Size::insert(['name'=>$value])->id);
        //       toastr.success('Data Add successfully!!');
        //       }
        //       return redirect()->back();
        //     }
        // }

   public function store(Request $request)
{
  $allSize =   Size::all();
  $sizeData;
  foreach($request->input('name') as $key => $value) {

    $checkData =   Size::where('size',$value)->first();
    if ($checkData === null) {
      Size::insert(['size'=>$value]);
     }
   }
      return response()->json(['success'=>'done']);


}


    // update size


    public function updateSize(Request $req)
    {
      $id = $req->input('id');

      $name = $req->input('name');
      $result= Size::where('id',$id)->update([
        'size'=>$name
       ]);

       if($result==true){
         return 1;
       }
       else{
        return 0;
       }
    }

    // public function updateSize(Request $req)
    // {
    //   $id = $req->input('id');
    //
    //   $name = $req->input('name');
    //   $namechg = explode(',',$name);
    //   $size = json_encode($namechg);
    //   $result= Size::where('id',$id)->update([
    //    	'name'=>$size
    //    ]);
    //
    //    if($result==true){
    //      return 1;
    //    }
    //    else{
    //     return 0;
    //    }
    // }

    // details size
    public function sizeDetails(Request $req)
    {
      $id = $req->input('id');
      $data=Size::where('id','=',$id)->get();
      return $data;
    }




    // change sub cat Status

    public function sizeStatus(Request $req){
        $id = $req->input('id');
        // $getCategory = Category::find($id);
        $data = Size::where('id',$id)->first();
        if ($data->status == 1) {
          $status = 0;
        } else {
          $status = 1;
        }
        $result = Size::where('id',$id)->update(['status'=>$status]);
            if ($result==true) {
              return 1;
            } else {
              return 0;
            }
    }


  // get allcategory

  public function allsize()
  {
    $result = Size::all();
    return $result;
  }
  // delete categoryDelete

    public function sizeDelete(Request $req)
    {
      $id = $req->input('id');
      $result = Size::where('id','=',$id)->delete();
      if ($result==true) {
        return 1;
      } else {
        return 0;
      }
    }

  }
