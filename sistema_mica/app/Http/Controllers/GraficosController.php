<?php

namespace App\Http\Controllers;

use App\Models\DatosGraficos;
use App\Models\Establecimientos;
use App\Models\Estaciones;
use App\Models\TicketSalida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraficosController extends Controller
{
    public function graficos($id)
    {
        $estaciones = Estaciones::where('id_establecimiento',$id)->get();
        $establecimiento = Establecimientos::where('id_establecimiento',$id)->first();
        $haceDosMeses = strtotime('-2 months');
        $ema1t = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_t as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            //->groupBy(DB::raw('UNIX_TIMESTAMP(fecha_unix)/(30* 60)'))
            ->get();
        $ema1t_final = json_encode($ema1t);

        $ema2t = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_t as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            //->groupBy(DB::raw('UNIX_TIMESTAMP(fecha_unix)/(30* 60)'))
            ->get();
        $ema2t_final = json_encode($ema2t);

        $ema1h = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_h as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1h_final = json_encode($ema1h);

        $ema2h = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_h as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2h_final = json_encode($ema2h);

        $ema1p = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_p as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1p_final = json_encode($ema1p);

        $ema2p = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_p as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2p_final = json_encode($ema2p);

        $ema1v = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_v as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1v_final = json_encode($ema1v);

        $ema2v = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S1_v as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2v_final = json_encode($ema2v);

        $ema1r = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S2_r as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1r_final = json_encode($ema1r);

        $ema2r = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S2_r as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2r_final = json_encode($ema2r);

        $ema1n = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S2_n as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1n_final = json_encode($ema1n);

        $ema2n = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S2_n as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2n_final = json_encode($ema2n);

        $ema1l = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S3_n as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1l_final = json_encode($ema1l);

        $ema2l = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S3_n as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2l_final = json_encode($ema2l);

        $ema1i = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S5_i as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1i_final = json_encode($ema1i);

        $ema2i = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S5_i as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2i_final = json_encode($ema2i);

        $ema1tb = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S6_t as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1tb_final = json_encode($ema1tb);

        $ema2tb = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S6_t as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2tb_final = json_encode($ema2tb);

        $ema1co2 = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S7_c02 as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[0]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema1co2_final = json_encode($ema1co2);

        $ema2co2 = DatosGraficos::select(
            DB::raw("(fecha_unix * 1000) as date"),
            "S7_c02 as value"
        )
            ->where('nodo_establecimientos.nodo',$estaciones[1]->mac)
            ->where('fecha_unix', '>=', $haceDosMeses)
            ->orderBy("fecha_unix")
            ->get();
        $ema2co2_final = json_encode($ema2co2);

        return view('graficos',
            compact(
                'ema1t_final',
                'ema2t_final',
                'ema1h_final',
                'ema2h_final',
                'ema1p_final',
                'ema2p_final',
                'ema1v_final',
                'ema2v_final',
                'ema1r_final',
                'ema2r_final',
                'ema1n_final',
                'ema2n_final',
                'ema1l_final',
                'ema2l_final',
                'ema1i_final',
                'ema2i_final',
                'ema1tb_final',
                'ema2tb_final',
                'ema1co2_final',
                'ema2co2_final',
                'establecimiento',
                'estaciones'
            ));
    }
    public function getDataEMA1($id)
    {
        $estaciones = Estaciones::where('id_establecimiento',$id)->get();
        $datosEma1 = DatosGraficos::where('nodo',$estaciones[0]->mac)->orderBy('fecha_unix','desc')->get();
        return response()->json($datosEma1);
    }

    public function getDataEMA2($id)
    {
        $estaciones = Estaciones::where('id_establecimiento',$id)->get();
        $datosEma2 = DatosGraficos::where('nodo',$estaciones[1]->mac)->orderBy('fecha_unix','desc')->get();
        return response()->json($datosEma2);
    }

    public function getDataEMAComp($id)
    {
        $estaciones = Estaciones::where('id_estacion',$id)->first();
        $datosEma = DatosGraficos::where('nodo',$estaciones->mac)->orderBy('fecha_unix','desc')->get();
        return response()->json($datosEma);
    }

    public function comparativa()
    {
        $estaciones = Estaciones::select(
            "establecimientos.nombre as establecimiento",
            "establecimientos.comuna as establecimiento_comuna",
            "estaciones.nombre as estaciones",
            "estaciones.mac as estaciones_mac",
            "estaciones.id_estacion",
            "establecimientos.rbd"
        )
            ->join("establecimientos", "establecimientos.id_establecimiento", "=", "estaciones.id_establecimiento")
            ->join("nodo_establecimientos", "nodo_establecimientos.nodo", "=", "estaciones.mac")
            ->groupBy("estaciones.mac")
            ->get();
        return view('comparativa', compact('estaciones'));
    }

    public function cargarGrafico( Request $request)
    {
        $emas = $request->get("emas");
        $emasFinal = Estaciones::select(
            "establecimientos.nombre as establecimiento",
            "establecimientos.comuna as establecimiento_comuna",
            "estaciones.nombre as estaciones",
            "estaciones.mac as estaciones_mac",
            "estaciones.id_estacion",
            "establecimientos.rbd"
        )
            ->wherein('id_estacion',$emas)
            ->join('establecimientos','establecimientos.id_establecimiento','=','estaciones.id_establecimiento')
            ->get();
        $tres_meses=time()-7889229;
        foreach ($emasFinal as $key => $value) {
            ${"emat$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S1_t as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emat_final$key"} = json_encode(${"emat$key"});

            ${"emah$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S1_h as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emah_final$key"}= json_encode(${"emah$key"});

            ${"emap$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S1_p as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emap_final$key"} = json_encode(${"emap$key"});

            ${"emav$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S1_v as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emav_final$key"} = json_encode(${"emav$key"});

            ${"emar$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S2_r as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emar_final$key"} = json_encode(${"emar$key"});

            ${"eman$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S2_n as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"eman_final$key"} = json_encode(${"eman$key"});

            ${"emal$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S3_n as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emal_final$key"} = json_encode(${"emal$key"});

            ${"emai$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S5_i as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emai_final$key"} = json_encode(${"emai$key"});

            ${"ematb$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S6_t as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"ematb_final$key"} = json_encode(${"ematb$key"});

            ${"emaco2$key"} = DatosGraficos::select(
                DB::raw("(fecha_unix * 1000) as date"),
                "S7_c02 as value"
            )
                ->where('nodo_establecimientos.nodo',$value->estaciones_mac)
                ->where('nodo_establecimientos.fecha_unix','>=',$tres_meses)
                ->orderBy("fecha_unix")
                ->get();
            ${"emaco2_final$key"} = json_encode(${"emaco2$key"});

        }
        $variable = array();
        foreach ($emasFinal as $key => $value){
            $variable["emat_final".$key] = ${"emat_final$key"};
            $variable["emah_final".$key] = ${"emah_final$key"};
            $variable["emap_final".$key] = ${"emap_final$key"};
            $variable["emav_final".$key] = ${"emav_final$key"};
            $variable["emar_final".$key] = ${"emar_final$key"};
            $variable["eman_final".$key] = ${"eman_final$key"};
            $variable["emal_final".$key] = ${"emal_final$key"};
            $variable["emai_final".$key] = ${"emai_final$key"};
            $variable["ematb_final".$key] = ${"ematb_final$key"};
            $variable["emaco2_final".$key] = ${"emaco2_final$key"};
        }
        $variable["emasFinal"]= $emasFinal;
        $resumenhtml = view('grafica_comp',$variable)->render();
        $response = array(
            'graficohtml' => $resumenhtml
        );
        return response()->json($response);
    }
    public function ticketSalida()
    {
        return view('ticket');
    }

    public function guardarTicket(Request $request)
    {
        TicketSalida::create($request->all());
        return "ok";
    }
}
