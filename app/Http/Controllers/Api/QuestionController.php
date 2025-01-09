<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Question, Form, AllowedDomain};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Auth};

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'choice_type'=>'required|in:"short answer", "paragraph", "date", "multiple choice", "dropdown", "checkboxes"',
            'choices'=>'required_if:choice_type, "multiple choice", "dropdown", "checkboxes"|array',
        ]);

        if($validator->fails())
        {
            return response()->json(["message"=>"invalid field", "errors"=>$validator->errors()], 422);
        }
        else
        {


            // Mengambil id dari slug yang user beri
            $form = Form::where("slug", $slug)->first();

            if($form)
            {
                //  Ambil id form
                $form_id = $form->id;

                // Menambahkan form_id ke input user
                $request->merge(["form_id"=>$form_id]);

                //  choices yang awalnya array jadi string
                $input = $request->all();
                if(isset($input["choices"]))
                {
                    $input["choices"] = implode(",", $input["choices"]);
                }

                $question = Question::create($input);
                //  todo Make Condition of Create Question
                //  LKS - 18
                //  Kondisi ketika user tak diizinkan
                $userEmail = Auth::user()->email;
                $userDomain = explode('@', $userEmail[1]);

                $allowedDomain = allowedDomain::where('form_id', $form_id)->first();

                //  Response
                $response = response()->json(
                [
                    "message"=>"Add question success",
                    "question"=>[
                        "name"=>$question->name,
                        "choice_type"=>$question->choice_type,
                        "is_required"=>$question->is_required,
                        "choices"=>$question->choices,
                        "form_id"=>$question->form_id,
                        "id"=>$question->id,
                    ]
                ], 200);

                if($allowedDomain)
                {
                    $allowedDomain = allowedDomain::where('form_id', $form_id)->pluck('domain')->toArray();

                    if(in_array($userDomain, $allowedDomain))
                    {

                        return $response;
                    }
                    else
                    {
                        return response()->json(["message"=>"Forbidden access"], 403);
                    }
                }
                else
                {
                    return $response;
                }
            }
            else
            {
                return response()->json(["message"=>"Form not found"], 404);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Request $request, $slug, $id)
    {
        $form = Form::where('slug', $slug)->first();
        if(!$form)
        {
            return response()->json(["message"=>"Form not found"], 404);
        }

        if(!$question = Question::where('id', $id)->first())
        {
            return response()->json(["message"=>"Quetion not found"], 404);
        }
        $user = $request->user();
        if($form->creator_id!=$user->id)
        {
            return response()->json(["message"=>"Forbidden access"], 403);
        }
        //  Klo quetion id ketemu
        //  Klo quetion id gak ketemu
        $question->delete();
        return response()->json(["message"=>"Remove question success"], 200);
    }
}
