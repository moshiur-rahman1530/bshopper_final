<div class="container-fluid offer pt-5">
        <div class="row px-xl-5">

        @php 
           $categoriesall = App\Models\Category::orderBy('id','DESC')->take(2)->get();
           $ProductAttrs = App\Models\Product::with('attributes')->orderBy('id','DESC')->take(2)->get();
          
        @endphp
                   @if(isset($ProductAttrs))
                     @foreach($ProductAttrs as $ProductAttr)
                        @php
                         $products = App\Models\Category::where('id',(int) $ProductAttr->product_cat)->get();
                         $discountText = $ProductAttr->attributes->take(2);


                         
                         @endphp
                         @foreach($products as $cat)

                        
                         <div class="col-md-6 pb-4">
                                <div class="position-relative bg-secondary text-center text-md-right text-white mb-2 py-5 px-5 h-100">
                                    <img src="{{$cat->cat_img}}" alt="" class="img-fluid">
                                    <div class="position-relative" style="z-index: 1;">
                                    @if(count($discountText)>0)
                                        <h5 class="text-uppercase text-primary mb-3">{{$discountText[0]['discount']}}&#2547; off Avilable</h5>
                                        @else
                                        <h5 class="text-uppercase text-primary mb-3">0&#2547; off Avilable</h5>
                                    @endif
                                        <h1 class="mb-4 font-weight-semi-bold">{{$cat->cat_name}}</h1>
                                        <a href="{{url('/categoryPage/'.$cat->id)}}" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                       

                        @endforeach

                        @endif


                     


       
            <!-- <div class="col-md-6 pb-4">
                <div class="position-relative bg-secondary text-center text-md-right text-white mb-2 py-5 px-5">
                    <img src="img/offer-1.png" alt="">
                    <div class="position-relative" style="z-index: 1;">
                        <h5 class="text-uppercase text-primary mb-3">20% off the all order</h5>
                        <h1 class="mb-4 font-weight-semi-bold">Spring Collection</h1>
                        <a href="" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pb-4">
                <div class="position-relative bg-secondary text-center text-md-left text-white mb-2 py-5 px-5">
                    <img src="img/offer-2.png" alt="">
                    <div class="position-relative" style="z-index: 1;">
                        <h5 class="text-uppercase text-primary mb-3">20% off the all order</h5>
                        <h1 class="mb-4 font-weight-semi-bold">Winter Collection</h1>
                        <a href="" class="btn btn-outline-primary py-md-2 px-md-3">Shop Now</a>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
