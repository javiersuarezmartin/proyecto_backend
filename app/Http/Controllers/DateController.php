<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Validator;
use App\Models\Date;
use App\Models\User;
use Carbon\Carbon;

class DateController extends Controller
{
    public function store(Request $request)
    {
        // Validación
        $validation =  Validator::make($request->all(), [
            'date' => 'required|string',
            'hour' => 'required|string',            
        ]);       
        
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        } else {
            $dateAux = [
                'user_id' => $request->user()->id,
                'date' => $request->date,
                'hour' => $request->hour, 
            ]; 
            // Comprobamos que no esté reservada la hora o que se haya intentado reservar casi al mismo tiempo.
            if(empty(Date::where('date', '=', $request->date)->where('hour', '=', $request->hour)->get()->toArray())) {
                // Asignamos el modelo de la cita con los datos.
                $date = Date::create($dateAux);
                return response()->json($date, 201);
            } else {
                return response()->json('Error, otro usuario ya ha reservado esta hora en este intervalo', 409);
            }
        } 
    }

    public function getReservedHours($date)
    {       
        $reservedHours = Date::where('date', '=', $date)->get('hour')->toArray();        
        return response()->json($reservedHours, 200);
    }

    public function getAllDates(Request $request)
    {       
        $user = $request->user()->id;
        //dd($user);
        $now = Carbon::now();
        $alldates = Date::where([
                            ['user_id', '=', $user], 
                            ['date', '>=', $now->format('Y-m-d')]
                        ])
                        ->orderBy('date')
                        ->orderBy('hour')
                        ->get()
                        ->toArray();        
        //dd($alldates);

        if (empty($alldates)) {
            return response()->json($alldates, 404);
        } else {
            return response()->json($alldates, 200);
        }        
    }

    
    public function getDatesDay(Request $request, $date)
    {       
        $role = $request->user()->role;
        //dd($user);
        // Comprobamos que se trata de un admin 1.
        if($role == 1) {
            $datesDay = Date::where('date', '=', $date)
                            ->orderBy('hour')
                            ->get()
                            ->toArray();        
            //($datesDay);
            $datesDayAux = [];
            foreach($datesDay as $key => $value) {                
                $user = User::where('id', '=', $value['user_id'])->get()->toArray();                
                $value['user_name'] = $user[0]['name'];
                $datesDayAux [] = $value;
            }; 
            
            if (empty($datesDayAux)) {
                return response()->json($datesDay, 404);
            } else {
                return response()->json($datesDayAux, 200);
            };

        } else {
            return response()->json('Acceso restringido', 403);
        };
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user()->id;
        $cita = Date::find($id);
        //dd($cita->user_id);
        
        if (is_null($cita)) {
            return response()->json(['error' => 'Cita no encontrada'], 404);  
        } else {
            /* Comprobamos si este usuario tiene permisos */
            if ($cita->user_id == $user) {
                $cita->delete();
                return response()->json(['message' => 'Cita eliminada correctamente', 'data' => $cita], 200);  
            } else {
                return response()->json(['message' => 'Usuario no autorizado para esta cita'], 401);
            }
        };
    }
}
