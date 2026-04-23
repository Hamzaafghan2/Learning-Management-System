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
                    <li class="breadcrumb-item active" aria-current="page">Edit Permission</li>
                </ol>
            </nav>
        </div>
         
    </div>
    <!--end breadcrumb-->
 
    <div class="card">
        <div class="card-body p-4">
            <h5 class="mb-4">Edit Permission</h5>
            <form id="myForm" action="{{ route('update.permission') }}" method="post" class="row g-3" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $permissions->id }}">
                 <div class="form-group col-md-6">
                    <label for="input1" class="form-label">Permission Name</label>
                    <input type="text" name="name" class="form-control" id="input1" value="{{ $permissions->name }}" >
                </div>

                <div class="form-group col-md-6">
                    <label for="input1" class="form-label"> Group Name</label>
                    <select name="group_name" class="form-select mb-3" aria-label="Default select example">
                        <option selected="" disabled>Open this select menu</option>
                      
              <option value="Category"{{ $permissions->group_name == 'Category' ?'selected' :'' }} >Category</option>
               <option value="Instructor"{{ $permissions->group_name == 'Instructor' ?'selected' :'' }}>Instructor </option>
               <option value="Coupon"{{ $permissions->group_name == 'Coupon' ?'selected' :'' }}>Coupon</option>
               <option value="Setting"{{ $permissions->group_name == 'Setting' ?'selected' :'' }}>Setting</option>
               <option value="Orders"{{ $permissions->group_name == 'Orders' ?'selected' :'' }}>Orders</option>
               <option value="Report"{{ $permissions->group_name == 'Report' ?'selected' :'' }}>Report</option>
               <option value="Review"{{ $permissions->group_name == 'Review' ?'selected' :'' }}>Review</option>
               <option value="All User"{{ $permissions->group_name == 'All User' ?'selected' :'' }}>All User </option>
               <option value="Blog"{{ $permissions->group_name == 'Blog' ?'selected' :'' }}>Blog</option>
               <option value="Role and Permission" {{ $permissions->group_name == 'Role and Permission' ?'selected' :'' }}>Role and Permission</option> 
                        
                        
                    </select>
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