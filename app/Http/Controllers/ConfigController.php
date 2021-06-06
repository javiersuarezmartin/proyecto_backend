<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Validator;
use App\Models\Config;

class ConfigController extends Controller
{
    public function store(Request $request)
    {
        // Validación
        $validation =  Validator::make($request->all(), [
            'hour_start' => 'required|string',
            'hour_end' => 'required|string',
            'interval' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        } else {
            $getConfig = Config::select()->get()->toArray(); 
            //dd($getConfig);

            $allData = $request->all();
            // Asignamos el modelo de config con los datos
            $conf = Config::create($allData);

            //dd($conf);
            $resArr = [
                'message' => 'Succesfully configured!',
                'data' => $conf->toArray()
            ];          

            return response()->json($resArr, 201);            
        }
    }

    public function getConfig(Request $request)
    {    
        $actualConfig = Config::select()->first();            
                    
        if ($actualConfig == null) {
            return response()->json('Configuration not found', 404);
        } else {
            return response()->json($actualConfig, 200);
        };       
    }

    public function updateConfig(Request $request)
    {      
        $role = $request->user()->role;
        
        if ($role == 1) {
            // Validación
            $validation =  Validator::make($request->all(), [
                'hour_start' => 'required|string',
                'hour_end' => 'required|string',
                'interval' => 'required|integer',
            ]);

            if ($validation->fails()) {
                return response()->json($validation->errors(), 400);
            } else {
                $actualConfig = Config::select()->first();
                //dd($actualConfig);
                if ($actualConfig == null) {
                    return response()->json('Configuration not found', 404);
                } else {
                    $allData = $request->all();
                    // Asignamos el modelo de config con los datos
                    $updatedConfig = Config::select()->first()->update($allData);
                    
                    if($updatedConfig) {
                        $msg = 'Succesfully Updated!';
                    } else {
                        $msg = 'Error Updating!';
                    };                           

                    return response()->json($msg, 201);
                };                            
            };
        } else {
            return response()->json('Forbidden Access!', 403);
        };
    }  
}
