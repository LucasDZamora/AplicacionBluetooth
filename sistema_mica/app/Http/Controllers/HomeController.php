<?php

namespace App\Http\Controllers;

use App\Models\DatosGraficos;
use App\Models\DatosGraficosExperimentos;
use App\Models\Establecimientos;
use App\Models\Estaciones;
use App\Models\Experimentos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $emas = Estaciones::where('id_establecimiento',Auth::user()->id_establecimiento)->get();
        return view('home',compact('emas'));
    }

    public function crearExperimento(Request $request)
    {
        $experimento = new Experimentos();
        return $this->extracted($request, $experimento);
    }

    public function guardarNombre(Request $request)
    {
        $nombre = $request->nombre;
        $mac = $request->mac;

        foreach ($mac as $key => $value) {
            $estacion = Estaciones::where('mac',$value)->first();
            $estacion->nombre = $nombre[$key];
            $estacion->save();
        }

        return "ok";
    }

    public function getExperimento($id_establecimiento)
    {
        $experimentos = Experimentos::select(
            "experimentos.*",
            DB::raw("DATE_FORMAT(experimentos.fecha_inicio,'%d-%m-%Y %H:%i') as fechaInicio"),
            DB::raw("DATE_FORMAT(experimentos.fecha_termino,'%d-%m-%Y %H:%i') as fechaTermino"),
            DB::raw("DATE_FORMAT(experimentos.created_at,'%d-%m-%Y %H:%i') as Creado"),
            DB::raw("COALESCE(COUNT(nodo_establecimientos_experimentos.id), 0) as registros"),
        )
            ->leftJoin('nodo_establecimientos_experimentos', function($join){
                $join->on(DB::raw("nodo_establecimientos_experimentos.nodo COLLATE utf8mb4_unicode_ci"), '=', DB::raw("experimentos.ema COLLATE utf8mb4_unicode_ci"));
            })
            ->where("experimentos.id_establecimiento", "=", $id_establecimiento)
            ->where("experimentos.estado", "=", 1)
            ->groupBy('experimentos.id_experimentos')
            ->get();
        return response()->json($experimentos);
    }

    public function verGrafico($id_experimento)
    {
        $experimentos = Experimentos::where('id_experimentos',$id_experimento)->first();
        $estacion = Estaciones::where('mac',$experimentos->ema)->first();
        $establecimiento = Establecimientos::where('id_establecimiento',$estacion->id_establecimiento)->first();
        $ema1t = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_t as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1t_final = json_encode($ema1t);

        $ema1h = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_h as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1h_final = json_encode($ema1h);


        $ema1p = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_p as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1p_final = json_encode($ema1p);

        $ema1v = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_v as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1v_final = json_encode($ema1v);

        $ema1r = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S2_r as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1r_final = json_encode($ema1r);

        $ema1n = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S2_n as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1n_final = json_encode($ema1n);

        $ema1l = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S3_n as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1l_final = json_encode($ema1l);

        $ema1i = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S5_i as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1i_final = json_encode($ema1i);

        $ema1tb = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S6_t as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1tb_final = json_encode($ema1tb);

        $ema1co2 = DatosGraficosExperimentos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S7_c02 as value"
        )
            ->where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)
            ->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])
            ->orderBy("fecha_unix")
            ->get();
        $ema1co2_final = json_encode($ema1co2);

        return view('graficos_experimentos',
            compact(
                'ema1t_final',
                'ema1h_final',
                'ema1p_final',
                'ema1v_final',
                'ema1r_final',
                'ema1n_final',
                'ema1l_final',
                'ema1i_final',
                'ema1tb_final',
                'ema1co2_final',
                'experimentos',
                'estacion',
                'establecimiento'
            ));
    }
    public function getGrafico($id_experimento)
    {
        $experimentos = Experimentos::where('id_experimentos',$id_experimento)->first();
        $datosEma1 = DatosGraficosExperimentos::where('nodo_establecimientos_experimentos.nodo',$experimentos->ema)->whereBetween('nodo_establecimientos_experimentos.fecha_unix', [$experimentos->inicio_unix, $experimentos->termino_unix])->orderBy('fecha_unix')->get();
        return response()->json($datosEma1);
    }

    public function cambiarContrasena(Request $request)
    {
        $clave = User::find($request->id_usuario);
        $clave->password = Hash::make($request->ncontrasena);
        $clave->save();
        return "ok";
    }

    public function eliminarExperimento(Request $request)
    {
        $experimento = Experimentos::find($request->id_experimento);
        $experimento->estado = 0;
        $experimento->save();
        return "ok";
    }

    public function datosExperimento(Request $request)
    {
        $experimento = Experimentos::find($request->id_experimento);
        return response()->json($experimento);
    }

    public function datosEma(Request $request)
    {
        $datoEma = DatosGraficos::find($request->id_ema);
        return response()->json($datoEma);
    }

    public function editarExperimento(Request $request)
    {
        $experimento = Experimentos::find($request->id_experimento);
        return $this->extracted($request, $experimento);
    }

    public function editarDato(Request $request)
    {
        $id = $request->id_dato;
        $dato_ema = DatosGraficos::find($id);
        $flag = 0;
        $fecha = $dato_ema->reading_time;
        if (isset($request->S1_t_C)){
            $dato_ema->S1_t = null;
            $flag = 1;
        }
        if (isset($request->S1_h_C)){
            $dato_ema->S1_h = null;
            $flag = 1;
        }
        if (isset($request->S1_p_C)){
            $dato_ema->S1_p = null;
            $flag = 1;
        }
        if (isset($request->S1_v_C)){
            $dato_ema->S1_v = null;
            $flag = 1;
        }
        if (isset($request->S2_r_C)){
            $dato_ema->S2_r = null;
            $flag = 1;
        }
        if (isset($request->S2_n_C)){
            $dato_ema->S2_n = null;
            $flag = 1;
        }
        if (isset($request->S3_n_C)){
            $dato_ema->S3_n = null;
            $flag = 1;
        }
        if (isset($request->S5_i_C)){
            $dato_ema->S5_i = null;
            $flag = 1;
        }
        if (isset($request->S7_c02_C)){
            $dato_ema->S7_c02 = null;
            $flag = 1;
        }
        $dato_ema->reading_time = $fecha;
        $dato_ema->save();
        if ($flag == 1)
            return "ok";
        else
            return "no";
    }

    /**
     * @param Request $request
     * @param $experimento
     * @return string
     */
    public function extracted(Request $request, $experimento): string
    {
        $experimento->nombre = $request->get('nombre');
        $experimento->ema = $request->get('ema');
        $experimento->fecha_inicio = $request->get('fecha_inicio');
        $experimento->inicio_unix = strtotime($request->get('fecha_inicio'));
        $experimento->fecha_termino = $request->get('fecha_termino');
        $experimento->termino_unix = strtotime($request->get('fecha_termino'));
        $experimento->descripcion = $request->get('descripcion');
        $experimento->id_establecimiento = $request->get('id_establecimiento');
        $experimento->save();
        return "ok";
    }

    public function importarTxt(Request $request)
    {
        $preview = [];
        $config = [];
        $errors = [];

        $file = $request->file('importar_datos');
        if (!$file) {
            $out = [
                'initialPreview' => $preview,
                'initialPreviewConfig' => $config,
                'initialPreviewAsData' => true,
                'error' => 'No se subió el archivo.'
            ];
            return response()->json($out);
        }

        $handle = fopen($file->getRealPath(), "r");
        $importados = 0;
        $saltados = 0;
        $mac_no_encontradas = [];

        while (($line = fgets($handle)) !== false) {
            $datos = [];
            preg_match_all('/(\w+)=([^\s]+)/', $line, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $datos[$match[1]] = $match[2];
            }
            // Validar modo estación
            if ($datos['estado'] != 0) {
                $saltados++;
                continue;
            }

            // Validar MAC en estaciones
            if (!isset($datos['nodo'])) {
                $saltados++;
                continue;
            }
            $estacion = Estaciones::where('id_establecimiento',$request->id_establecimiento)->where('mac', $datos['nodo'])->first();

            if (!$estacion) {
                $saltados++;
                if (!in_array($datos['nodo'], $mac_no_encontradas)) {
                    $mac_no_encontradas[] = $datos['nodo'];
                }
                continue;
            }

            // Validar campos requeridos para identificar duplicados
            if (!isset($datos['S9_rtc'], $datos['S10_f'])) {
                $errors[] = "Datos incompletos en línea: $line";
                continue;
            }

            // Buscar si ya existe en la base de datos
            $existe = DatosGraficos::where('nodo', $datos['nodo'])
                ->where('S9_rtc', $datos['S9_rtc'])
                ->where('S10_f', $datos['S10_f'])
                ->exists();

            if ($existe) {
                $saltados++;
                continue;
            }

            // --- CONVERTIR FECHAS ---
            $reading_time_str = null;
            $fecha_unix = null;
            try {
                // S10_f = DD:MM:YYYY, S9_rtc = HH:MM:SS (pero algunos valores son HH:MM:S, etc.)
                [$dia, $mes, $anio] = explode(':', $datos['S10_f']);
                $hora = $datos['S9_rtc'];

                // Normalizar hora a formato HH:MM:SS
                $hms = explode(':', $hora);
                $h = str_pad($hms[0], 2, '0', STR_PAD_LEFT);
                $m = str_pad($hms[1], 2, '0', STR_PAD_LEFT);
                $s = isset($hms[2]) ? str_pad($hms[2], 2, '0', STR_PAD_LEFT) : '00';
                $hora_normalizada = "$h:$m:$s";

                // Crear objeto Carbon (YYYY-MM-DD HH:MM:SS)
                $reading_time = Carbon::createFromFormat('d:m:Y H:i:s', "$dia:$mes:$anio $hora_normalizada");
                $fecha_unix = $reading_time->timestamp;
                $reading_time_str = $reading_time->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $reading_time_str = null;
                $fecha_unix = null;
                $errors[] = "Error al convertir fecha/hora en línea: $line";
            }

            // Guardar en la base de datos
            DatosGraficos::create([
                'S1_h' => $datos['S1_h'] ?? null,
                'S1_p' => $datos['S1_p'] ?? null,
                'S1_v' => $datos['S1_v'] ?? null,
                'S1_t' => $datos['S1_t'] ?? null,
                'S2_r' => $datos['S2_r'] ?? null,
                'S2_n' => $datos['S2_n'] ?? null,
                'S3_n' => $datos['S3_n'] ?? null,
                'S4_long' => $datos['S4_long'] ?? null,
                'S4_lat' => $datos['S4_lat'] ?? null,
                'S4_a' => $datos['S4_a'] ?? null,
                'S4_v' => $datos['S4_v'] ?? null,
                'S4_h' => $datos['S4_h'] ?? null,
                'S5_i' => $datos['S5_i'] ?? null,
                'S6_t' => $datos['S6_t'] ?? null,
                'S7_c02' => $datos['S7_c02'] ?? null,
                'S8_n' => $datos['S8_n'] ?? null,
                'S9_rtc' => $datos['S9_rtc'] ?? null,
                'S10_f' => $datos['S10_f'] ?? null,
                'nodo' => $datos['nodo'] ?? null,
                'reading_time' => $reading_time_str,
                'fecha_unix' => $fecha_unix,
            ]);

            $importados++;
        }

        fclose($handle);

        // Mensaje resumen para fileinput
        $mensaje = "Importación finalizada. <br>";
        $mensaje .= "<b>$importados</b> registros importados.<br>";
        $mensaje .= "<b>$saltados</b> registros omitidos (por duplicidad o MAC no registrada).<br>";
        if (!empty($mac_no_encontradas)) {
            $mensaje .= "Las siguientes MAC no existen en estaciones:<br><ul>";
            foreach ($mac_no_encontradas as $mac) {
                $mensaje .= "<li>$mac</li>";
            }
            $mensaje .= "</ul>";
        }
        if (!empty($errors)) {
            $mensaje .= "Errores:<br><ul>";
            foreach ($errors as $err) {
                $mensaje .= "<li>" . htmlspecialchars($err) . "</li>";
            }
            $mensaje .= "</ul>";
        }

        $preview[] = "<div class='alert alert-info'>$mensaje</div>";

        $out = [
            'initialPreview' => $preview,
            'initialPreviewConfig' => $config,
            'initialPreviewAsData' => true
        ];

        return response()->json($out);
    }

    public function importarTxtExperimentos(Request $request)
    {
        $preview = [];
        $config = [];
        $errors = [];

        $file = $request->file('importar_datos_experimentos');
        if (!$file) {
            $out = [
                'initialPreview' => $preview,
                'initialPreviewConfig' => $config,
                'initialPreviewAsData' => true,
                'error' => 'No se subió el archivo.'
            ];
            return response()->json($out);
        }

        $handle = fopen($file->getRealPath(), "r");
        $importados = 0;
        $saltados = 0;
        $mac_no_encontradas = [];

        while (($line = fgets($handle)) !== false) {
            $datos = [];
            preg_match_all('/(\w+)=([^\s]+)/', $line, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $datos[$match[1]] = $match[2];
            }
            // Validar modo estación
            if ($datos['estado'] != 1) {
                $saltados++;
                continue;
            }

            // Validar MAC en estaciones
            if (!isset($datos['nodo'])) {
                $saltados++;
                continue;
            }
            $estacion = Estaciones::where('id_establecimiento',$request->id_establecimiento)->where('mac', $datos['nodo'])->first();

            if (!$estacion) {
                $saltados++;
                if (!in_array($datos['nodo'], $mac_no_encontradas)) {
                    $mac_no_encontradas[] = $datos['nodo'];
                }
                continue;
            }

            // Validar campos requeridos para identificar duplicados
            if (!isset($datos['S9_rtc'], $datos['S10_f'])) {
                $errors[] = "Datos incompletos en línea: $line";
                continue;
            }

            // Buscar si ya existe en la base de datos
            $existe = DatosGraficosExperimentos::where('nodo', $datos['nodo'])
                ->where('S9_rtc', $datos['S9_rtc'])
                ->where('S10_f', $datos['S10_f'])
                ->exists();

            if ($existe) {
                $saltados++;
                continue;
            }

            // --- CONVERTIR FECHAS ---
            try {
                // S10_f = DD:MM:YYYY, S9_rtc = HH:MM:SS (pero algunos valores son HH:MM:S, etc.)
                [$dia, $mes, $anio] = explode(':', $datos['S10_f']);
                $hora = $datos['S9_rtc'];

                // Normalizar hora a formato HH:MM:SS
                $hms = explode(':', $hora);
                $h = str_pad($hms[0], 2, '0', STR_PAD_LEFT);
                $m = str_pad($hms[1], 2, '0', STR_PAD_LEFT);
                $s = isset($hms[2]) ? str_pad($hms[2], 2, '0', STR_PAD_LEFT) : '00';
                $hora_normalizada = "$h:$m:$s";

                // Crear objeto Carbon (YYYY-MM-DD HH:MM:SS)
                $reading_time = Carbon::createFromFormat('d:m:Y H:i:s', "$dia:$mes:$anio $hora_normalizada");
                $fecha_unix = $reading_time->timestamp;
                $reading_time_str = $reading_time->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $reading_time_str = null;
                $fecha_unix = null;
                $errors[] = "Error al convertir fecha/hora en línea: $line";
            }

            // Guardar en la base de datos
            DatosGraficosExperimentos::create([
                'S1_h' => $datos['S1_h'] ?? null,
                'S1_p' => $datos['S1_p'] ?? null,
                'S1_v' => $datos['S1_v'] ?? null,
                'S1_t' => $datos['S1_t'] ?? null,
                'S2_r' => $datos['S2_r'] ?? null,
                'S2_n' => $datos['S2_n'] ?? null,
                'S3_n' => $datos['S3_n'] ?? null,
                'S4_long' => $datos['S4_long'] ?? null,
                'S4_lat' => $datos['S4_lat'] ?? null,
                'S4_a' => $datos['S4_a'] ?? null,
                'S4_v' => $datos['S4_v'] ?? null,
                'S4_h' => $datos['S4_h'] ?? null,
                'S5_i' => $datos['S5_i'] ?? null,
                'S6_t' => $datos['S6_t'] ?? null,
                'S7_c02' => $datos['S7_c02'] ?? null,
                'S8_n' => $datos['S8_n'] ?? null,
                'S9_rtc' => $datos['S9_rtc'] ?? null,
                'S10_f' => $datos['S10_f'] ?? null,
                'nodo' => $datos['nodo'] ?? null,
                'reading_time' => $reading_time_str,
                'fecha_unix' => $fecha_unix,
            ]);

            $importados++;
        }

        fclose($handle);

        // Mensaje resumen para fileinput
        $mensaje = "Importación finalizada. <br>";
        $mensaje .= "<b>$importados</b> registros importados.<br>";
        $mensaje .= "<b>$saltados</b> registros omitidos (por duplicidad o MAC no registrada).<br>";
        if (!empty($mac_no_encontradas)) {
            $mensaje .= "Las siguientes MAC no existen en estaciones:<br><ul>";
            foreach ($mac_no_encontradas as $mac) {
                $mensaje .= "<li>$mac</li>";
            }
            $mensaje .= "</ul>";
        }
        if (!empty($errors)) {
            $mensaje .= "Errores:<br><ul>";
            foreach ($errors as $err) {
                $mensaje .= "<li>" . htmlspecialchars($err) . "</li>";
            }
            $mensaje .= "</ul>";
        }

        $preview[] = "<div class='alert alert-info'>$mensaje</div>";

        $out = [
            'initialPreview' => $preview,
            'initialPreviewConfig' => $config,
            'initialPreviewAsData' => true
        ];

        return response()->json($out);
    }
}
