@extends('layouts.app')

@section('content')
<div class="container my-5">
    @include('userComponent.feature')
    @include('userComponent.categoryProduct')
    @include('userComponent.discount')
    @include('userComponent.products')
    @include('userComponent.subscribe')
    @include('userComponent.archiveProduct')
    @include('userComponent.vendor')
</div>
@endsection

 @section('script')
   <script type="text/javascript">


function sendSubscription() {
    var valid;	
    valid = validateContact();
    if(valid) {
       var email=$("#subscribeemail").val();

        axios.post('/subscribe',{email:email}).then(function(response){

            console.log(response.data);
            if(response.data == 1){
                toastr.success('Subscription added successfull!!');
                $("#subscribeemail").val('');
                $("#mail-status").html(data);
            }

            if(response.data==0){
                toastr.warning('Already subscribed using this email!!');
                $("#subscribeemail").val('');
            }
           
        }).catch(function(error){

        })


        // $.ajax({
        //     headers: {
        //          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     },
        //     url: "/subscribe",
        //     data:'email='+$("#email").val(),
        //     type: "POST", 
        //     success:function(data){
        //         if(data.success == false) {
        //             toastr.warning('Already subscribed using this email!!');
        //         } else {
        //             $("#mail-status").html(data);
        //             toastr.success('Contact info store success!!');
        //             $("#email").val('');
        //         }
                
        //     },
        //     error:function (){
                
        //     }
        // });



    }
}


function validateContact() {
    var valid = true;	
   
    if(!$("#subscribeemail").val()) {
        $("#email-info").html("(*Email fieldrequired)");
        $("#subscribeemail").css('background-color','#FFFFDF');
        valid = false;
    }
    if(!$("#subscribeemail").val().match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)) {
        $("#email-info").html("(invalid)");
        $("#subscribeemail").css('background-color','#FFFFDF');
        valid = false;
    }
    return valid;
}

</script>

@endsection
