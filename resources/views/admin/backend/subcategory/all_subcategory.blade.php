@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content py-4" style="min-height: 100vh; background-color: #f8f9fa;">
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-flex align-items-center justify-content-between mb-4"> 
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">All SubCategories</li>
                </ol>
            </nav>
        </div>

        <div>
           
            <a href="{{ route('add.subcategory') }}" class="btn btn-primary">
                <i class="bx bx-plus-circle"></i> Add SubCategory
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">SubCategory List</h5>
            <span class="badge bg-light text-dark">{{ count($subcategory) }} Total</span>
        </div>

        <div class="card-body bg-white">
            <div class="table-responsive">
                <table id="example" class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Sl</th>
                            <th>Category Name</th>
                            <th>SubCategory Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subcategory as $key => $item)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $item->category ? $item->category->category_name : 'No Category' }}</td>
                            <td>{{ $item->subcategory_name }}</td>
                            <td class="text-center">
                                <a href="{{ route('edit.subcategory', $item->id) }}" class="btn btn-sm btn-info me-2">
                                    <i class="bx bx-edit"></i> Edit
                                </a>
                                <a href="{{ route('delete.subcategory', $item->id) }}" 
                                   class="btn btn-sm btn-danger" id="delete">
                                    <i class="bx bx-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
