{{-- @extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"> 
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">edit Category</li>
                </ol>
            </nav>
        </div>
         
    </div>
    <!--end breadcrumb-->
 
    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">edit Category</h5>
             <form id="myForm" action="{{route('store.category')}}" method="post" enctype="multipart/form-data" class="row g-3">
                @csrf
                
                <input type="text" name="id" value="{{$category->id}}">
                <div class="form-group col-md-6">
                    <label for="input1" class="form-label">Category Name</label>
                    <input type="text" name="category_name" class="form-control" id="input1" value="{{$category->category_name}}" >
                </div>

                <div class="col-md-6">
                </div>

                  <div class="form-group col-md-6">
                    <label for="input2" class="form-label form-group">Category Image </label>
                    <input class="form-control" name="image" type="file" id="image">
                </div>

                <div class="col-md-6"> 
                    <img id="showImage" src="{{asset($category->image)}}" alt="Admin" class="rounded-circle p-1 bg-primary" width="80"> 

                </div>


             
                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
          <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                      
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function (){
        $('#myForm').validate({
            rules: {
                category_name: {
                    required : true,
                }, 
                image: {
                    required : true,
                }, 
                
            },
            messages :{
                category_name: {
                    required : 'Please Enter Category Name',
                }, 
                image: {
                    required : 'Please Select Category Image',
                }, 
                 

            },
            errorElement : 'span', 
            errorPlacement: function (error,element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight : function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            },
        });
    });
    
</script>

@endsection
 --}}

@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3"> 
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"> SMTP Setting</li>
                </ol>
            </nav>
        </div>
         
    </div>
    <!--end breadcrumb-->
 
    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">SMTP Setting</h5>
            
            <form id="myForm" action="{{ route('update.smtp') }}" method="post" class="row g-3" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="id" value="{{ $smtp->id }}">

                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">Mailer </label>
                    <input type="text" name="mailer" class="form-control" id="input1" value="{{ $smtp->mailer }}"  >
                </div>

                <div class="form-group col-md-6">
                    <label for="input1" class="form-label">Host </label>
                    <input type="text" name="host" class="form-control" id="input1" value="{{ $smtp->host }}"  >
                </div>

                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">Port </label>
                    <input type="text" name="port" class="form-control" id="input1" value="{{ $smtp->port }}"  >
                </div>

                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">Uername </label>
                    <input type="text" name="username" class="form-control" id="input1" value="{{ $smtp->username }}"  >
                </div>

                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">password </label>
                    <input type="text" name="password" class="form-control" id="input1" value="{{ $smtp->password }}"  >
                </div>

                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">encryption </label>
                    <input type="text" name="encryption" class="form-control" id="input1" value="{{ $smtp->encryption }}">
                </div>

                
                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">From Address </label>
                    <input type="text" name="from_address" class="form-control" id="input1" value="{{ $smtp->from_address }}">
                </div>

                
             
                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-3">
          <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                      
                    </div>
                </div>
            </form>
        </div>
    </div>


   
   
</div>
 

@endsection