<?php

namespace App\Http\Controllers;

use App\Models\TeacherService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->teacherService = new TeacherService();
    }

    public function index(Request $request) {
        $params = [];
        $params['teachers'] = $this->teacherService->getAvailable();
        return view('teacher.index', $params);
    }

    public function edit(Request $request) {
        $params = [];
        $this->initVueOptions($request, $params);
        // get data
        if($id = $request->input('id')){
            $params['teacher'] = $this->teacherService->getInfoById($id);
        }
        return view('teacher.edit', $params);
    }

    public function update(Request $request) {
        // prepare update data
        $data = [];
        foreach (['name', 'sex', 'phoneNum'] as $key){
            if ($request->has($key)) $data[$key] = $request->input($key);
        }
        // update or create
        if($id = $request->input('id')){
            $this->teacherService->updateOne($id, $data);
        }else{
            $this->teacherService->createOne($data);
        }
        return redirect("/teacher/index");
    }

    public function delete(Request $request) {
        if($id = $request->input('id')) {
            // disable
            $this->teacherService->disableOne($id);
        }
        return redirect("/teacher/index");
    }

}
