<?php

namespace App\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Models\Category;
use App\Models\User;
use App\Validations\CategoryValidation;
use App\Views\Admin\Layouts\Footer;
use App\Views\Admin\Layouts\Header;
use App\Views\Admin\Components\Notification;
use App\Views\Admin\Pages\Category\Create;
use App\Views\Admin\Pages\Category\Edit;
use App\Views\Admin\Pages\Category\Index;
use PgSql\Result;

class CategoryController
{


    // hiển thị danh sách
    public static function index()
    {
        // giả sử data là mảng dữ liệu lấy được từ database
        // $data = [
        //     [
        //         'id' => 1,
        //         'name' => 'Category 1',
        //         'status' => 1
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'Category 2',
        //         'status' => 1
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => 'Category 3',
        //         'status' => 0
        //     ],

        // ];

        $category = new Category();
        $data = $category->getAllCategory();

        Header::render();
        Notification::render();
        NotificationHelper::unset();
        // hiển thị giao diện danh sách
        Index::render($data);
        Footer::render();
        
    }


    // hiển thị giao diện form thêm
    public static function create()
    {
        // var_dump($_SESSION);
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        // hiển thị form thêm
        Create::render();
        Footer::render();
    }


    // xử lý chức năng thêm
    public static function store()
    {
        // validation các trường dữ liệu

        $is_valid = CategoryValidation::create();

        if (!$is_valid) {
            NotificationHelper::error('store', 'thêm loại sản phẩm thất bại');
            header('location: /admin/categories/create');
            exit();
        }

        $name = $_POST['name'];
        $status = $_POST['status'];
        //    kiểm tra tên loại có tồn tại chưa => ko được trùng tên

        $category = new Category();
        $is_exist = $category->getOneCategoryByName($name);

        if ($is_exist) {
            NotificationHelper::error('store', 'Tên loại sản phẩm đã tồn tại');
            header('location: /admin/categories/create');
            exit();
        }

        // thực hiện thêm
        $data=[
            'name' => $name,
           'status' => $status,
        ];
        $Result=$category->createCategory($data);
        if ($Result) {
            NotificationHelper::success('store', 'Thêm loại sản phẩm thành công');
            header('location: /admin/categories');
            exit();
        }
        else {
            NotificationHelper::error('store', 'Thêm loại sản phẩm thất bại');
            header('location: /admin/categories/create');
            exit();
        }
    }


    // hiển thị chi tiết
    public static function show()
    {
    }


    // hiển thị giao diện form sửa
    public static function edit(int $id)
    {
        // giả sử data là mảng dữ liệu lấy được từ database
        // $data = [
        //     'id' => $id,
        //     'name' => 'Category 1',
        //     'status' => 1
        // ];
        $category = new Category();
        $data = $category->getOneCategory($id);

        if(!$data){
            NotificationHelper::error('edit', 'Loại sản phẩm không tồn tại');
            header('location: /admin/categories');
            exit();
        }
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        // hiển thị form sửa
        Edit::render($data);
        Footer::render();

        // if ($data) {
        //     Header::render();
        //     // hiển thị form sửa
        //     Edit::render($data);
        //     Footer::render();
        // } else {
        //     header('location: /admin/categories');
        // }
    }


    // xử lý chức năng sửa (cập nhật)
    public static function update(int $id)
    {
        // echo 'Thực hiện cập nhật vào database';

        $is_valid = CategoryValidation::edit();

        if (!$is_valid) {
            NotificationHelper::error('update', 'Cập nhật  loại sản phẩm thất bại');
            header("location: /admin/categories/$id");
            exit();
        }

        $name = $_POST['name'];
        $status = $_POST['status'];
        //    kiểm tra tên loại có tồn tại chưa => ko được trùng tên

        $category = new Category();
        $is_exist = $category->getOneCategoryByName($name);

        if ($is_exist) {
            if($is_exist['id']!=$id){
                NotificationHelper::error('update', 'Tên loại sản phẩm đã tồn tại');
            header("location: /admin/categories/$id");
            exit();
            }
            // NotificationHelper::error('store', 'Tên loại sản phẩm đã tồn tại');
            // header('location: /admin/categories/create');
            // exit();
        }

        // thực hiện Cập nhật




        $data=[
            'name' => $name,
           'status' => $status,
        ];


        $Result=$category->updateCategory($id,$data);
        if ($Result) {
            NotificationHelper::success('update', 'Cập nhật sản phẩm thành công');
            header('location: /admin/categories');
            exit();
        }
        else {
            NotificationHelper::error('update', 'Cập nhật loại sản phẩm thất bại');
            header("location: /admin/categories/$id");
            exit();
        }
    }


    // thực hiện xoá
    public static function delete(int $id)
    {
        $category =new Category();
        $result=$category->deleteCategory($id);
        if($result){
            NotificationHelper::success('delete', 'Xóa loại sản phẩm thành công');
            header('location: /admin/categories');
            exit();
        }else{
            NotificationHelper::error('delete', 'Xóa loại sản phẩm thất bại');
            header('location: /admin/categories');
            exit();
        }

    }
}
