<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Form, AllowedDomain};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Auth};

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::get();

        return response()->json(["message"=>"Get all form s success", "forms"=>$forms], 200);
    }
    
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

    public function show($slug)
    {
        //  Ber relasi dengan function questions di model Form
        //  Mencari column slug dengan value dari user
        $form = Form::with('questions')->where('slug', $slug)->first();

        //  Bila form ada
        if($form)
        {
            //  Get loggined user data
            $userEmail = Auth::user()->email;
            //  Cut email and remove before @ and skip one char
            $userDomain = explode('@', $userEmail)[1];

            //  Cek id form ini ada atau tidak di table allowed_domains
            $allowedDomain = AllowedDomain::where('form_id', $form->id)->first();
            if($allowedDomain)
            {
                //  Cek domain user ada atau tidak
                $allowedDomain = AllowedDomain::where('form_id', $form->id)->pluck('domain')->toArray();
                if(in_array($userDomain, $allowedDomain))
                {
                    return response()->json(["message"=>"Get form success", "form"=>$form], 200);
                }
                //  Klo user kagak ada
                else
                {
                    return response()->json(["message"=>"Forbidden access"], 403);
                }
            }
            //  Bila form id tak ada di allowed_domains
            else
            {
                return response()->json(["message"=>"Get form success", "form"=>$form], 200);
            }
        }
        //  Bila form tak ada
        else
        {
            return response()->json(["message"=>"Form not found"], 404);
        }
    }
}
