@extends('admin.layouts.app')
@section('title','Brands')
@section('content')



        <div class="container-fluid m-0 ">

            <div class="row">
                <!-- for custome image add -->
                <!-- <button  id="addNewPhotoBtnId" class="btn my-3 btn-sm btn-primary font-weight-bold"><i class="fas fa-plus"></i> Add New </button> -->

                <!-- select image from image gallery -->
                <!-- <button class="btn my-3 btn-sm btn-primary font-weight-bold" onclick="filemanager.bulkSelectFile('myBulkSelectCallback')"><i class="fas fa-plus"></i> Add New Images</button> -->

                <!-- <input type="file" name="galery_images[]" id="galery_images" multiple> -->

               <div class="col-md-6 col-12 mb-4">
                    <div class="input-group custom-file-button">
                        <label class="input-group-text" for="inputGroupFile">Add New Photo</label>
                        <input type="file" name="galery_images[]" class="form-control" accept="image/*" id="inputGroupFile" multiple>
                    </div>
               </div>
               <div class="col-md-6 col-12 m-0 mb-4">
               <button type="submit" class="input-group-text btn btn-sm btn-success" id="submitGaleryImg">Upload</button>
               </div>

            </div>

            <div class="row g-2 showPhotos">

            </div>
            <!-- <i class="	fas fa-redo" aria-hidden="true"></i> -->
            <button class="btn btn-sm btn-success font-weight-bold" id="LoadMoreImage">Load More </button>
        
        </div> 

 <!-- Modal -->
 <div class="modal fade" id="PhotoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Photo</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> 

                </div>

                <div class="modal-body">

              

                <form id="multiple-image-upload-preview-ajax" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="card shadow">
                            <div class="card-header">
                                <h4 class="card-title fw-bold">Images Upload</h4>
                            </div>

                            <div class="card-body">
                                <label for="file"> Image(s) <span class="text-danger">*</span> </label>
                                <input type="file" name="images[]" id="images" multiple class="form-control">
                            </div>

                           
                                <div class="preview-image row p-4" id="preview-image"> </div>

                            <div class="card-footer">
                            <button type="submit" class="btn btn-sm btn-success" id="submitdata">Submit</button>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script type="text/javascript">

$(document).ready(function (e) {



    loadPhoto();

    function loadPhoto(){
        axios.get('/getImages').then(function(response){

            if (response.status==200 && response.data) {
                var data = response.data;
                $.each(response.data, function(index, value) {
                    // console.log(value);

                    $("<div class='col-12 col-sm-6 col-md-4 p-2'>").html(
                        "<div class='gallery'><a target='_blank' href="+ value['path'] + "><img data-id="+ value['id']+" class='' src=" + value['path'] + "> </a>"+
                        "<button data-id="+ value['id']+" data-photo="+ value['path']+" class='btn deletePhoto btn-sm btn-danger ml-5'> Delete "+ value['id']+" </button></div>"
                        ).appendTo('.showPhotos');

                        $('.deletePhoto').on('click',function (event) {
                            let id=$(this).data('id');
                            // console.log(id);
                            let photo=$(this).data('photo');

                            PhotoDelete(photo,id);

                            event.preventDefault();
                        })
                   
                });
            }else{
                toastr.error('Photo Load Faild');
            }

        }).catch(function(error){
            toastr.error('Photo Load Faild');
        })
    }

    $('#LoadMoreImage').on('click',function () {
           let loadMoreBtn=$(this);
           let FirstImgID= $(this).closest('div').find('img').data('id');
           console.log(FirstImgID);
           LoadByID(FirstImgID,loadMoreBtn);
        })



    $('#addNewPhotoBtnId').click(function(){
        $('#PhotoModal').modal('show');
    })


    var  ImgID=0;
        function LoadByID(FirstImgID,loadMoreBtn){
            ImgID=ImgID+10;
            let PhotoID=ImgID+FirstImgID;
            let URL="/getImages/"+PhotoID

             loadMoreBtn.html("<div class='spinner-border spinner-border-sm' role='status'></div>")
             axios.get(URL).then(function (response) {
                 loadMoreBtn.html("Load More");
                $.each(response.data, function(i, value) {
                    $("<div class='col-md-3 col-lg-4 col-12 p-1'>").html(
                        "<div class='gallery'><a target='_blank' href='img_5terre.jpg'><img data-id="+ value['id']+" class='' src=" + value['path'] + "> </a>"+
                        "<button data-id="+ value['id']+" data-photo="+ value['path']+" class='btn deletePhoto btn-sm btn-danger ml-5'> Delete "+ value['id']+"</button></div>"
                    ).appendTo('.showPhotos');
                });

                $('.deletePhoto').on('click',function (event) {
                    let id=$(this).data('id');
                    // console.log(id);
                    let photo=$(this).data('photo');
                    // console.log('data=',photo);

                    PhotoDelete(photo,id);

                    event.preventDefault();
                })

            }).catch(function (error) {

            })

        }


    function PhotoDelete(image, id){
        let url ='/image-delete';
        let formData=new FormData();
        formData.append('path',image);
        formData.append('id',id);
        axios.post(url,{id:id,path:image}).then(function(response){
            if(response.status==200 && response.data==1){
                // loadPhoto();
                toastr.success('Image Delete Success');
                // loadPhoto();
                window.location.href="/upload-images";
            }
            else{
                toastr.error('Delete Fail Try Again');
            }

        }).catch(function(error){
            toastr.error('Something went wrong');
        })

       }
   
   $.ajaxSetup({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
   });
  
 






// add new photo

$('#submitGaleryImg').click(function(){
  
  var galeryImg = $('#inputGroupFile')[0];
let UpdateTotalFiles = $('#inputGroupFile')[0].files.length;

  addGaleryImg(galeryImg,UpdateTotalFiles);
})


function addGaleryImg(galeryImg,UpdateTotalFiles){

var form_data = new FormData(); 
form_data.append("UpdateTotalFiles", UpdateTotalFiles) ;
for (let i = 0; i < UpdateTotalFiles; i++) {
  form_data.append('img' + i, galeryImg.files[i]);
    }


  $('#submitGaleryImg').html("<div class='spinner-border spinner-border-sm' role='status'></div>")
  axios.post('/uploadGaleryImage', form_data).then(function(response){
      $('#submitGaleryImg').html("Upload");
      if(response.status==200){
              if (response.data == 1) {
                toastr.success('Add Success');
                loadPhoto();
                $("#inputGroupFile").val(null);
            } else {
                toastr.error('Add Fail');
                loadPhoto();
                $("#inputGroupFile").val(null);
            }

            if(response.data=='Image is required'){
                // loadPhoto();
                toastr.warning('Image is required');
            }

          }
  }).catch(function(error){
    toastr.error(error);
  });
}



// CategoryImage.onchange = evt => {
//   const [file] = CategoryImage.files
//   if (file) {
//     CategoryImagePreview.src = URL.createObjectURL(file)
//   }
// }

});
</script>
@endsection
