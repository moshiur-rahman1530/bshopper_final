<?php
use App\Models\Rating;
 ?>
<div class="container-fluid pt-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">Trandy Products</span></h2>
        </div>
        <div class="row px-xl-5 pb-3">
        
          @foreach($allProduct as $products)
            <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                <div class="card product-item border-0 mb-4">
                    <div class="card-header product-img position-relative overflow-hidden bg-transparent border p-0">
                    @php $imageData = json_decode($products->product_img);  @endphp
                        <img class="img-fluid w-100" src="{{$imageData[0]}}" alt="">
                    </div>
                    <div class="card-body border-left border-right text-center p-0 pt-4 pb-3">
                        <h6 class="text-truncate mb-3">{{$products->product_name}}</h6>
                        <p class="mb-3 card-text">{{Str::limit($products->product_des, 40, $end='.......')}}</p>

                        
                        <div class="d-flex justify-content-center">
                            <h6>${{$products->product_price}}</h6><h6 class="text-muted ml-2"><del>${{$products->product_price}}</del></h6>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-light border">
                        <!-- <a href="{{url('/detailsProduct/'.$products->id)}}" class="btn btn-sm text-dark p-0"><i class="fas fa-eye text-primary mr-1"></i>View Detail</a> -->
                        <div class="ratings">
                                @if(count($products->ratings)>0)
                                    @foreach($products->ratings as $rat)
                                    <!-- $avg = avg($rat->star_rating)/count($products->ratings); -->
                                        @php
                                            $av = round($rat->avg('star_rating'),2);
                                            $coutcarttotal = App\Models\Rating::ratAvg($rat->product_id);
                                        @endphp
                                    @endforeach
                                        @for($i = 1; $i <= 5; $i++)
                                                @if($coutcarttotal < $i)
                                                    @if (round($coutcarttotal) == $i)
                                                        <li class="list-inline-item me-0 small" style="font-size:12px; padding:0px!important; margin:0px!important;"><i
                                                                class="fas fa-star-half-alt text-warning"></i></li>
                                                        @continue
                                                    @endif
                                                    <li class="list-inline-item me-0 small" style="font-size:12px; padding:0px!important; margin:0px!important;"><i
                                                            class="far fa-star text-warning"></i></li>
                                                    @continue
                                                @endif
                                                <li class="list-inline-item me-0 small" style="font-size:12px; padding:0px!important; margin:0px!important;"><i
                                                        class="fas fa-star text-warning"></i></li>
                                            @endfor

                                    ({{ count($products->ratings) }})

                                @endif
                        </div>
                        <div class="b-cart">
                        <a href="{{url('/detailsProduct/'.$products->id)}}" data-id="{{$products->id}}" class="btn btn-sm text-dark p-0"><i class="addToCartBtn fas fa-shopping-cart text-primary mr-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
