<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Form, AllowedDomain};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //? Membahas regex
        //  regex:/^[...]+$/
        //  [<berisi aturan>]
        //  a-z A-Z 0-9 . - (yang diizinkan)
        //  . (titik) butuh \ (backslash) diawal karena karakter khusus '\.'
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'slug'=>'required|unique:forms,slug|regex:/^[a-zA-Z0-9\.\-]+$/',
            'allowed_domains'=>'array',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                "message"=>"Invalid field",
                "errors"=>$validator->errors()
            ], 422);
        }
        else
        {
            //  Menambah data dari request
            //  Menambah creator_id dengan value id dari token sactum
            $request->merge(['creator_id'=>auth()->id()]);

            //  Create new data
            $form = Form::create($request->all());

            // Jika user memasukan allowed_domains
            if($request->has('allowed_domains'))
            {
                //  Loop hingga array allowed_domains yang dimasukkan user terpecah
                foreach($request->allowed_domains as $domain)
                {
                    //  Menambah data ke table allowed_domains
                    AllowedDomain::create(['domain'=>$domain, 'form_id'=>$form->id]);
                }
            }
            return response()->json([
                "message"=>"Create form success",
                "form"=>[
                    "name"=>$form->name,
                    "slug"=>$form->slug,
                    "description"=>$form->description,
                    "limit_one_response"=>$form->limit_one_response,
                    "creator_id"=>$form->creator_id,
                    "id"=>$form->id,
                ]
            ], 200);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
