<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{

    public function index(){
        
        $students = Student::all();
        $message= 'No records found';
        if($students->count() > 0){
            $data = [
               'status' =>  200,
                'students' => $students
            ];
            
            return response()->json($data,200);      
    
            }
        
        else{
            return response()->json($message,404);
        }

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:191',
            'course' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:student,email', //validating Unique Email
            'phone' => 'required|digits:10|unique:student,phone'       //validating Unique Phone
        ]);
        
        // $email = Student::find($request->email); This will not work because Find(id) only looks up by primary ID, not by email.

        
        // $email = $request->email;
        // if(Student::where('email', $email)->exists()){
        //     return response()->json(['message' => 'Email Already Exists!'],409);     //Validating Unique Email Manually
        // }
        // else{

            if($validator->fails()){
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->messages()
                ],422);
            }
            else{
                $student = Student::create([
                    'name' => $request->name,
                    'course' => $request->course,
                    'email' => $request->email,
                    'phone' => $request->phone
                ]);
    
                if($student){
                    return response()->json([
                        'status' => 200,
                        'message' => 'Student created successfully'
                    ], 200);
                }else{
                    return response()->json([
                        'status' => 500,
                        'message' => 'Something went wrong!'
                    ], 500);
    
                }
            }
        // }
    }

    public function show($id){
        $student = Student::find($id);
        if($student){
            return response()->json([
                'status' => 200,
                'student' => $student
            ],200);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No records found!'
            ],404);
        }
    }

    public function edit($id){
        $student = Student::find($id);

        if($student){
            return response()->json([
                'status' => 200,
                'student' => $student 
            ], 200);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No Such Record Found!!'
            ]);
        }
    }

    public function update(Request $request, int $id){
        $student = Student::find($id);
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:191',
            'course' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:student,email,' . $id, //Checking if same email exists in DB except its own email while updating
            'phone' => 'required|digits:10|unique:student,phone,' . $id     //Same happening for phone number
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 422,
                'errors' => $validator -> messages()
            ],422);
        }else{
            
            $student = Student::find($id);
            
            if($student){
                $student->update([
                    'name' => $request->name,
                    'course' => $request->course,
                    'email' => $request->email,
                    'phone' => $request->phone
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Student updated successfully'
                ],200);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'No such record found!!'
                ],404);
            }
        }
    }

    public function destroy($id){
        $student = Student::find($id);

        if($student){
            $student->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Student deleted successfully!'
            ],200);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'No such record found!!'
            ],404);

        }
    }

    public function search($query)
{
    $students = Student::where('name', 'LIKE', "%{$query}%")
        ->orWhere('email', 'LIKE', "%{$query}%")
        ->orWhere('course', 'LIKE', "%{$query}%")
        ->get();

    if ($students->isEmpty()) {
        return response()->json(['students' => []], 200); // Return empty array if no results
    }

    return response()->json(['students' => $students], 200);
}

    // public function verify_email($email){
    //     $student = Student::find($email);
    //     if($student){
    //         return response()->json(['message' => 'Email Already Exists!'],200);
    //     }


    // }
}
