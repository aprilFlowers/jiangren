<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index');

// login
Route::get('/login', 'LoginController@index');
Route::post('/login', 'LoginController@login');
Route::get('/logout', 'LoginController@logout');

// auth
Route::get('/auth/permission', 'Auth\PermissionController@index');
Route::get('/auth/permission/edit', 'Auth\PermissionController@edit');
Route::post('/auth/permission/update', 'Auth\PermissionController@update');
Route::get('/auth/permission/delete', 'Auth\PermissionController@delete');
Route::get('/auth/role', 'Auth\RoleController@index');
Route::get('/auth/role/edit', 'Auth\RoleController@edit');
Route::post('/auth/role/update', 'Auth\RoleController@update');
Route::get('/auth/role/delete', 'Auth\RoleController@delete');
Route::get('/auth/user', 'Auth\StaffController@index');
Route::get('/auth/user/edit', 'Auth\StaffController@edit');
Route::post('/auth/user/update', 'Auth\StaffController@update');
Route::get('/auth/user/delete', 'Auth\StaffController@delete');
Route::get('/auth/user/changePWD', 'Auth\StaffController@changePWD');
Route::post('/auth/user/changePWD/update', 'Auth\StaffController@pwdUpdate');

// teacher
Route::get('/teacher/index', 'TeacherController@index');
Route::get('/teacher/index/edit', 'TeacherController@edit');
Route::post('/teacher/update', 'TeacherController@update');
Route::get('/teacher/index/delete', 'TeacherController@delete');

// student
Route::get('/student/index', 'StudentController@index');
Route::post('/student/index', 'StudentController@index');
Route::get('/student/index/edit', 'StudentController@edit');
Route::post('/student/update', 'StudentController@update');
Route::get('/student/index/delete', 'StudentController@delete');

//course
Route::get('/course/subject', 'CourseController@subject');
Route::get('/course/subject/edit', 'CourseController@subjectEdit');
Route::post('/course/subject/update', 'CourseController@subjectUpdate');
Route::get('/course/subject/delete', 'CourseController@subjectDelete');
Route::get('/course/index', 'CourseController@index');
Route::post('/course/index', 'CourseController@index');
Route::get('/course/index/edit', 'CourseController@edit');
Route::post('/course/index/update', 'CourseController@update');
Route::get('/course/index/delete', 'CourseController@delete');
Route::post('/course/index/clickCourse', 'CourseController@clickCourse');
Route::get('/course/index/clickCourse', 'CourseController@clickCourse');
Route::get('/course/timetable', 'CourseController@timetable');
Route::post('/course/timetable', 'CourseController@timetable');
Route::get('/course/markTimetable/saveData', 'CourseController@saveTimetableData');
Route::get('/course/markTimetable/deleteData', 'CourseController@deleteTimetableData');
