@extends('admin.admin_layout')
@section('admin_content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-crosshairs-gps"></i>
            </span> Quản Lý Sản Phẩm
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="mdi mdi-timetable"></i>
                    <span><?php
                    $today = date('d/m/Y');
                    echo $today;
                    ?></span>
                </li>
            </ul>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Bảng Danh Sách Chi Tiết Sản Phẩm {{ $product->product->product_name }}
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>

                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th> #ID </th>
                            <td>{{ $product->product->product_id }}</td>
                        </tr>
                        <tr>
                            <th>Tên Sản Phẩm</th>
                            <td>{{ $product->product->product_name }}</td>
                        </tr>
                        <tr>
                            <th>Tên Thể Loại</th>
                            <td>{{ $product->product->category->category_name }}</td>
                        </tr>
                        <tr>
                            <th>Đơn Vị</th>
                            @php
                            $product_unit = '';
                            switch ($product->product->product_unit) {
                                case '0':
                                    $product_unit = 'Con';
                                    break;
                                case '1':
                                    $product_unit = 'Phần';
                                    break;
                                case '2':
                                    $product_unit = 'khay';
                                    break;
                                case '3':
                                    $product_unit = 'Túi';
                                    break;
                                case '4':
                                    $product_unit = 'Kg';
                                    break;
                                case '5':
                                    $product_unit = 'Gam';
                                    break;
                                case '6':
                                    $product_unit = 'Combo';
                                    break;
                                default:
                                    $product_unit = 'Bug Rùi :<';
                                    break;
                            }
                        @endphp
                          <td>{{ $product_unit }}</td>
                        </tr>
                        <tr>
                            <th>Lượng Bán</th>
                            <td>{{ $product->product->product_unit_sold }}</td>
                        </tr>
                        <tr>
                            <th>Giá</th>
                            <td>{{ $product->product->product_price }}</td>
                        </tr>
                        <tr>
                            <th>Ảnh Đại Diện</th>
                            <td><img style="object-fit: cover" width="40px" height="20px"
                                    src="{{ URL::to('public/fontend/assets/img/product/' . $product->product->product_image) }}"
                                    alt=""></td>
                        </tr>
                        <tr>
                            <th>Trạng Thái</th>
                            <td>@if($product->product->product_status == 1) Hoạt Động @else Tạm Khóa @endif</td>
                        </tr>
                        <tr>
                            <th>Nội Dung</th>
                            <td>{{ $product->product_details_content }}</td>
                        </tr>
                        <tr>
                            <th>Số Lượng Còn Lại</th>
                            <td>{{ $product->product_details_quantity }}</td>
                        </tr>
                        <tr>
                            <th>Xuất Xứ </th>
                            <td>{{ $product->product_details_origin }}</td>
                        </tr>
                        <tr>
                            <th>Món Ngon</th>
                            <td>{{ $product->product_details_delicious_foods }}</td>
                        </tr>
                        <tr>
                            <th>Ngày Thêm Vào</th>
                            <td>{{ $product->product->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Cập Nhật Lần Cuối</th>
                            <td>{{ $product->product->updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div style="display: flex;justify-content: space-between">
                    <div class="card-title col-sm-9">Thư Viện Ảnh Sản Phẩm {{ $product->product->product_name }}
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>
               
                <form action="{{ URL::to('/admin/product/insert-gallery/' . $product->product->product_id) }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Thêm Ảnh Vào Thư Viện Ảnh</label>
                        <input class="form-control" type="file" name="file[]" id="formFile" accept="image/*" multiple>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
              
                <table style="margin-top:20px " class="table table-bordered tab-gallery">
                    <form>
                        <input type="hidden" value="{{ $product->product->product_id }}" id="pro_id" name="pro_id">
                        @csrf
                        <thead>
                            <tr>
                                <th> #STT </th>
                                <th>Mã Sản Phẩm</th>
                                <th>Tên Ảnh</th>
                                <th>Hình Ảnh</th>
                                <th>Nội Dung Ảnh</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="loading_gallery_product"> 

                        </tbody>
                    </form>
                </table>

            </div>
        </div>
    </div>
    {{-- Toàn Bộ Script Liên Quan Đến Gallery --}}
    <script>
        $(document).ready(function() {
            /* Loading Gallrery On Table */
            load_gallery_product();

            function load_gallery_product() {
                var product_id = $("input[name='pro_id']").val();
                var _token = $("input[name='_token']").val();
                $.ajax({
                    url: '{{ url('admin/product/loading-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        product_id: product_id
                    },
                    success: function(data) {
                        $('#loading_gallery_product').html(data);
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            }
            /* Cập Nhật Tên Ảnh Gallery */
            $('.tab-gallery #loading_gallery_product').on('blur', '.update_gallery_product_name', function() {
                var gallery_id = $(this).data('gallery_id');
                var _token = $("input[name='_token']").val();
                var gallery_name = $(this).text();

                $.ajax({
                    url: '{{ url('admin/product/update-nameimg-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        gallery_id: gallery_id,
                        gallery_name: gallery_name,
                    },
                    success: function(data) {
                        message_toastr("success", "Tên Ảnh Đã Được Cập Nhật !");
                        load_gallery_product();
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            /* Cập Nhật Nội Dung Ảnh Gallery */
            $('.tab-gallery #loading_gallery_product').on('blur', '.edit_gallery_product_content', function() {
                var gallery_id = $(this).data('gallery_id');
                var _token = $("input[name='_token']").val();
                var gallery_content = $(this).text();

                $.ajax({
                    url: '{{ url('admin/product/update-content-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        gallery_id: gallery_id,
                        gallery_content: gallery_content,
                    },
                    success: function(data) {
                        message_toastr("success", "Nội Dung Ảnh Đã Được Cập Nhật !");
                        load_gallery_product();
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });


            /* Xóa Gallery */
            $('.tab-gallery #loading_gallery_product').on('click', '.delete_gallery_product', function() {
                var gallery_id = $(this).data('gallery_id');
                var _token = $("input[name='_token']").val();
                $.ajax({
                    url: '{{ url('admin/product/delete-gallery') }}',
                    method: 'post',
                    data: {
                        _token: _token,
                        gallery_id: gallery_id,
                    },
                    success: function(data) {
                        if(data == 'true'){
                            message_toastr("success", "Ảnh Đã Được Xóa !");
                        }else{
                            message_toastr("error", "Chỉ Có Quản Trị Viên Hoặc Quản Lý Mới Có Quyền Xóa Ảnh Này !");
                        }
                       
                        load_gallery_product();
                        load_gallery_product();
                     
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });

            });

            $('.tab-gallery #loading_gallery_product').on('change', '.up_load_file', function() {
                var gallery_id = $(this).data('gallery_id');
                var image = document.getElementById('up_load_file'+gallery_id).files[0];
                var form_data = new FormData();
                form_data.append("file",document.getElementById('up_load_file'+gallery_id).files[0]);
                form_data.append("gallery_id",gallery_id);

              
                $.ajax({
                    url: '{{ url('admin/product/update-image-gallery') }}',
                    method: 'post',
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data: form_data,
                    contentType:false,
                    cache:false,
                    processData:false,
                    success: function(data) {
                        message_toastr("success", "Cập Nhật Ảnh Thành Công !");
                        load_gallery_product();
                    },
                    error: function(data) {
                        alert("Nhân Ơi Fix Bug Huhu :<");
                    },
                });
            });

            $('#formFile').change(function() {
                var error = '';
                var files = $('#formFile')[0].files;

                if (files.length > 20) {
                    error += 'Bạn Không Được Chọn Quá 20 Ảnh';

                } else if (files.length == '') {
                    error += 'Vui lòng chọn ảnh';

                } else if (files.size > 10000000) {
                    error += 'Ảnh Không Được Lớn Hơn 10Mb';
                }

                if (error == '') {

                } else {
                    $('#formFile').val('');
                    message_toastr("error", ''+error+'');
                    return false;
                }

            });

        });
    </script>
@endsection
