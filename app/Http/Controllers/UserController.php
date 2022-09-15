<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Shipping;
use App\Models\Rating;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\User;
use App\Models\Banner;
use App\Models\Settings;
use App\Models\Cart;
use App\Models\Order;
use App\Models\order_details;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use PDF;
use File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function index()
    {
      $category = Category::with('subcategories')->get();
      $subcat = SubCategory::all();
      $allProduct= Product::with('ratings')->get();
      $latestProduct= Product::orderBy('id','desc')->get();

      // $banner=Banner::orderBy('id','DESC')->paginate(10);
      $banner=Banner::where('status',1)->limit(7)->orderBy('id','DESC')->get();
      $settings = Settings::first();

      // $data = DB::table('categories')->leftjoin('products', 'categories.id','=','products.product_cat')-> get();
      // return $category;

      return view('home', ['category'=>$category,'allProduct'=>$allProduct, 'newArive'=>$latestProduct,'subcat'=>$subcat,'banners'=>$banner,'settings'=>$settings]);
      // return view('home', ['category'=>$category, 'data'=>$data,'allProduct'=>$allProduct, 'newArive'=>$latestProduct,'subcat'=>$subcat]);
    }

    // public function getProductByCategory(Request $req)
      public function getProductByCategory()
    {

      // $data = DB::table('categories')->leftjoin('products', 'categories.id','=','products.product_cat')-> get();
      $category = Category::all();
      // $cat = $req->Category;
      // $category = Category::select('id')->get();
      $product = Product::where('product_cat',$category)->get();
      // return $product;
      return $category;
    }



    // category ldap_control_paged_result
    public function categoryPage(Request $req)
    {
      $id=$req->id;
      $category = Category::all();
      $catName = Category::find($id);
      $product = Product::where('product_cat',$id)->get();
      return view('categoryPage', ['category'=>$category, 'product'=>$product,'catName'=>$catName]);
    }

    // shipping page
    public function checkoutPage(Request $req)
    {
      $id=$req->id;
      $userId = Auth::id();
      $category = Category::all();
      $shipping = Shipping::all();
      $cart = Cart::where('user_id','=',$userId)->get();
      $catName = Category::find($id);
      $product = Product::where('product_cat',$id)->get();
      return view('shippingPage', ['category'=>$category, 'product'=>$product,'catName'=>$catName,'carts'=>$cart,'shipping'=>$shipping]);
    }
    // cart page
    public function ShippingCartDetailsPage(Request $req)
    {
      $id=$req->id;
      $category = Category::all();
      $shipping = Shipping::all();
      $catName = Category::find($id);
      $product = Product::where('product_cat',$id)->get();
      return view('cart', ['category'=>$category, 'product'=>$product,'catName'=>$catName,'shipping'=>$shipping]);
    }
  

        // sub category all products
        public function SubCategoryPage(Request $req)
        {
          $id=$req->id;
          $subcat = SubCategory::all();
          $cat = Category::all();
          $subcatname = SubCategory::find($id);
          $catName = Category::find($subcatname->cat_id);
          $product = Product::where('subcat_id',$id)->get();
          return view('SubCategoryPage', ['category'=>$cat,'subcat'=>$subcat, 'product'=>$product,'catName'=>$catName,'subcatname'=>$subcatname]);
        }


        // detailss product show
        public function detailsProduct(Request $req)
        {

          $socialShare = \Share::page(
              'https://www.nicesnippets.com/blog/laravel-custom-foreign-key-name-example',
              'Laravel Custom Foreign Key Name Example',
          )
          ->facebook()
          ->twitter()
          ->linkedin()
          ->whatsapp()
          ->telegram();



          $id=$req->id;
          $cat = Category::all();
          $subcatname = SubCategory::find($id);
          $product = Product::where('subcat_id',$id)->with('ratings')->get();
          
          $res = Product::where('id',$id)->with('attributes')->with('ratings')->first();
         
          // dd($detailsProducts);

          $detailsProducts['product']=
            DB::table('products')
            ->where(['status'=>1])
            ->where(['id'=>$id])
            ->get();
            

        foreach($detailsProducts['product'] as $list1){
            $detailsProducts['product_attrs'][$list1->id]=
                DB::table('product_attrs')
                ->leftJoin('sizes','sizes.id','=','product_attrs.size_id')
                ->leftJoin('colors','colors.id','=','product_attrs.color_id')
                ->where(['product_attrs.product_id'=>$list1->id])
                ->get();
        }
        $detailsProducts['related_product']=
            DB::table('products')
            ->where(['status'=>1])
            ->where('id','!=',$id)
            ->where(['product_cat'=>$detailsProducts['product'][0]->product_cat])
            ->get();
        foreach($detailsProducts['related_product'] as $list1){
            $detailsProducts['related_product_attr'][$list1->id]=
                DB::table('product_attrs')
                ->leftJoin('sizes','sizes.id','=','product_attrs.size_id')
                ->leftJoin('colors','colors.id','=','product_attrs.color_id')
                ->where(['product_attrs.product_id'=>$list1->id])
                ->get();
        }
        
        $detailsProducts['product_review']=
                DB::table('ratings')->where(['ratings.product_id'=>$detailsProducts['product'][0]->id])
                ->get();
        // $detailsProducts['product_review']=
        //         DB::table('products')
        //         ->leftJoin('ratings','ratings.product_id','=','products.id')
        //         ->get();

                // dd($detailsProducts);
          return view('detailsProduct',$detailsProducts, ['category'=>$cat, 'product'=>$product,'subcatname'=>$subcatname,'detailsProduct'=>$detailsProducts,'socialShare'=>$socialShare]);
        }

        // size wise color
        public function SizeByColor(Request $request , $id, $pid)
        {
          $result = DB::table('product_attrs')
          ->leftJoin('sizes','sizes.id','=','product_attrs.size_id')
          ->leftJoin('colors','colors.id','=','product_attrs.color_id')
          ->where(['product_attrs.size_id'=>$id])->where(['product_attrs.product_id'=>$pid])
          ->get();
          
          return $result;
        }
        // size color  price
        public function SizeColorByPrice(Request $request)
        {
          $id = $request->id;
          $size_id = $request->size_id;
          $color_id = $request->color_id;
          $result = DB::table('product_attrs')
          ->leftJoin('sizes','sizes.id','=','product_attrs.size_id')
          ->leftJoin('colors','colors.id','=','product_attrs.color_id')
          ->leftJoin('products','products.id','=','product_attrs.product_id')
          ->where(['product_attrs.size_id'=>$size_id])->where(['product_attrs.color_id'=>$color_id])->where(['product_attrs.product_id'=>$id])
          ->get();
          return $result;
        }
        
       

        public function reviewstore(Request $request){
          $product_id = $request->product_id;
          $name    = $request->name;
          $email   = $request->email;
          $comments= $request->review;
          $star_rating = $request->ratting;
          $result = Rating::insert(['product_id'=>$product_id, 'name'=>$name,'email'=>$email,'comments'=>$comments, 'star_rating'=>$star_rating]);
          return $result;
      }


      public function ShopPage(Request $request){
        $category = Category::all();
        $product = Product::with('ratings')->where('status',1);
        $productss =  DB::table('products')->leftJoin('product_attrs','product_attrs.product_id','=','products.id');
        $color = Color::all();
        $size = Size::all();

        // $product=$product->leftJoin('product_attrs','product_id','=','products.id')->get();
        // $product=$product->join('ratings','product_id','=','products.id')->get();

        $reqData=$request->sortVal;
        
        if($request->priceRange){
          $price_range = explode('-', $request->priceRange);
          $minPrice= doubleval($price_range[0]);
          $maxPrice= doubleval($price_range[1]);
          $product = Product::whereBetween('product_price',[$minPrice, $maxPrice])->orderBy('product_price','ASC');
        }else{
          $product->orderBy('id','Desc');
        }
     

        if($request->color){
          // $reqcol = array($request->color);
          $reqcol = $request->color;

          $dt = implode(',',$reqcol);

          // $p = Product::with('colors')->get();
          // dd($p);
      
          $product = Product::whereIn('color_id',$reqcol)->orderBy('product_price','ASC');
          // $product = Product::in_array($reqcol,'color_id')->orderBy('product_price','ASC');
        }

        if($request->size){
          $reqsize = array($request->size);
          $product = Product::where('size_id',json_encode($reqsize))->orderBy('product_price','ASC');       
          // dd($pro);
        }

        if($request->serchval){
          $product = Product::where('product_name', 'LIKE', '%' . $request->serchval . '%')->orderBy('product_price','ASC'); 
          // return $product;
        }
        // if($request->input('text')){
        //   $text = $request->input('text');
    
        //   $product = Product::where('product_name', 'LIKE', '%' . $text . '%')->orderBy('product_price','ASC');
        // }

        $priceRange = $request->priceRange;
          if (isset($request->sort)&& !empty($request->sort)) {

            if($request->sort=='latest'){
              // $product = Product::with('ratings')->orderBy('product_price','asc')->paginate(10);
              $product->orderBy('id','desc');
            }else if($request->sort=='priceHigh'){
              $product->orderBy('product_price','DESC');
            }else if($request->sort=='priceLow'){
              $product = Product::orderBy('product_price','ASC');
            }
            
          }else{
            // $product = Product::with('ratings')->get();
            // $product = Product::whereBetween('product_price', [0, 100])->get();
            $product->orderBy('id','Desc');
            // $productss->orderBy('id','Desc');
          }
        
       $products = $product->paginate(10);
       $productssss = $productss->paginate(10);

        return view('shop',['category'=>$category,'products'=>$products, 'productss'=>$productssss, 'colors'=>$color,'sizes'=>$size]);
     }


      public function search(Request $request) {
        $text = $request->input('serchval');
        $category = Category::all();
        $color = Color::all();
        $size = Size::all();
    
        $product = Product::where('product_name', 'LIKE', '%{$text}%')->paginate(10);
    
        return view('shop',['category'=>$category,'products'=>$product, 'colors'=>$color,'sizes'=>$size]);
        // return $product;
      }

      // contact us page

      public function getContactPage()
      {
        $category = Category::with('subcategories')->get();
        $subcat = SubCategory::all();
        return view('contact',['category'=>$category,'subcat'=>$subcat]);
      }


      public function userdashboard()
      {
        return view('profile.userHome');
      }

      public function userOrder()
      {

        $userEmail = Auth::user()->email;
        $userPhone = Auth::user()->phone;
        $userOrder = Order::where('email',$userEmail)->where('phone',$userPhone)->get();
        return view('profile.OrderList',['orders'=>$userOrder]);
      }

      public function userOrderDetails(Request $request, $id)
      {

        $OId = (int) $id;
        
        $order = Order::where('id',$OId)->first();
        
        $orderDetails = order_details::where('order_number',$OId)->get();
        $products = [];
        foreach($orderDetails as $details){
          $products[] =  DB::table('products')->leftJoin('product_attrs','product_attrs.product_id','=','products.id')->where('product_id',$details->product_id)->where('size_id',$details->size_id)->where('color_id',$details->color_id)->get();
        }

        return view('profile.Orderview',['order'=>$order, 'orderDetails'=>$orderDetails,'products'=>$products]);
      }


      // pdf genarator function

      public function generatePDF(Request $request, $id)
      {
        $OId = (int) $id;
        
        // $order = Order::where('id',$OId)->first();
        $order = Order::find($OId);
        $Settings = Settings::first();
        
        $orderDetails = order_details::where('order_number',$OId)->get();
        $products = [];
        foreach($orderDetails as $details){
          $products[] =  DB::table('products')->leftJoin('product_attrs','product_attrs.product_id','=','products.id')->where('product_id',$details->product_id)->where('size_id',$details->size_id)->where('color_id',$details->color_id)->get();
        }


          $data = [
              'title' => 'Welcome to ItSolutionStuff.com',
              'date' => date('m/d/Y')
          ];
            
          $pdf = PDF::loadView('pdf.myPDF', $data, ['order'=>$order, 'orderDetails'=>$orderDetails,'products'=>$products,'Settings'=>$Settings]);
      
          return $pdf->download('invoice.pdf');
      }

      public function viewPDF(Request $request, $id)
      {
        $OId = (int) $id;
        
        // $order = Order::where('id',$OId)->first();
        $order = Order::find($OId);
        $Settings = Settings::first();
        
        $orderDetails = order_details::where('order_number',$OId)->get();
        $products = [];
        foreach($orderDetails as $details){
          $products[] =  DB::table('products')->leftJoin('product_attrs','product_attrs.product_id','=','products.id')->where('product_id',$details->product_id)->where('size_id',$details->size_id)->where('color_id',$details->color_id)->get();
        }

          $data = [
              'title' => 'Welcome to Bshopper Online shope',
              'date' => date('m/d/Y')
          ];
            
          $pdf = PDF::loadView('pdf.myPDF', $data, ['order'=>$order, 'orderDetails'=>$orderDetails,'products'=>$products,'Settings'=>$Settings]);
         
          return $pdf->stream('invoice.pdf');
      
      }



      // user shipping area update

      public function updateusershippingareapage()
      {
        $shipping = Shipping::all();
       return view('profile.userShipping',['shipping'=>$shipping]);
      }
      public function updateshippingAddress(Request $request)
      {
        $areaId = $request->dataId;
      //  $result = Auth::user()->update(['shippingArea'=>$areaId]);
       $currentUser = Auth::user();
       $currentUser->shippingArea=$areaId;
       $result = $currentUser->save();

       $cdata = ['area'=>$request->shippingAddress,'amount'=>$request->shippingAmount];
       Session::put('shipping',$cdata);

       if ($result==true) {
        return 1;
      } else {
        return 0;
      }
      }

      public function getpassword()
      {
        return view('profile.changePassword');
      }

      public function changepassword(Request $request)
      {
        $oldPassword = $request->oldPass;
        $newPassword = $request->newPass;

        if(!Hash::check($oldPassword, Auth::user()->password)){
          return 0;
        }else{
            $request->user()->fill(['password' => Hash::make($newPassword)])->save(); //updating password into user table
           return 1;
        }

      }

      public function updateuserinfo(Request $request)
      {
        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;


        global $old_image;
        $old_image = Auth::user()->image;
        // dd($old_image);
        // dd($request->all());
        if($request->hasFile('image')){
            if (File::exists($old_image)) { 
                unlink($old_image);
            }
    
            $profileImage = $request->file('image');
            $profileImageSaveAsName = time() . Auth::id() . "-profile." . $profileImage->getClientOriginalExtension();
            $upload_path = 'profile_images/';
            $profile_image_url = $upload_path . $profileImageSaveAsName;
            $success = $profileImage->move($upload_path, $profileImageSaveAsName);

        }else{
            $profile_image_url = $old_image;
        }
        // dd($profile_image_url);

        $result= User::where('id','=',Auth::user()->id)->update([
          'name'=>$name,
          'email'=>$email,
          'phone'=>$phone,
          'image'=>$profile_image_url,
        ]);
   
        if($result==true){
          return $profile_image_url;
        }
        else{
         return 0;
        }
      }     
}

