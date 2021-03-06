<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Account;
use App\PersonCampaign;
use App\ReporteOperador;
use App\Campaign;
use Validator;
use DB;
use View;
use DateTime;
use App\Http\Requests;
use Session;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

if (Session::has('backUrl')) {
   Session::keep('backUrl');}
   

class reporteOperadorController extends Controller
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
        //$reporteDestinatario = new ReporteDestinatario;

        $currentuser= Auth::user(); 
        $PersonCampaign=PersonCampaign::where('idPerson', '=', $currentuser->id)->get();
        if($currentuser->createOperator($currentuser)==false)
        {
            return Redirect::to('/errors')->withInput()->withErrors('No tiene permisos para realizar esta operación');
        }
        
        return View::make('reporteOperador/form')->with('PersonCampaign', $PersonCampaign);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentuser= Auth::user(); 
        $data = Input::all();
        //dd($data);
        $reporteOperador = new ReporteOperador;
        if ($reporteOperador->isValid($data))
        {
            $where = "";
            //$numGroups= PersonGroupCampaign::where('idCampaign','=',$data['idCampaign'])->count();
             $numGroups = DB::table('Person')
            ->join('PersonPersonGroup', 'Person.id', '=', 'PersonPersonGroup.idPerson')
            ->join('CampaignPersonGroup', 'PersonPersonGroup.idPersonGroup', '=', 'CampaignPersonGroup.idPersonGroup')
            ->where('CampaignPersonGroup.idCampaign', '=', $data['idCampaign'])
            ->select('Person.id','firstName','lastName')
            ->distinct()->count();
            //dd($ppg);
            if(count($data['grupoOperador'])!=$numGroups) //si la cadena trae menos que la cantidad total de grupos de la campaña
            {
                if(count($data['grupoOperador'])==1)
                {
                    $ng=$data['grupoOperador'][0];
                    $where = 'WHERE "idPerson" IN ('.$ng.')'; 
                }
                else
                {
                    $ng=implode(",",$data['grupoOperador']);
                    $where = 'WHERE "idPerson" IN ('.$ng.')';  
                }
            }
        //dd($data);
        $idCampaign=$data['idCampaign'];

            if($data['dateTimeReporteStart']=='')
            {
                $campaign = Campaign::find($idCampaign);
                $date=date_create($campaign->dateTimeStart);
                //dd(date_format($date, 'Y-m-d'));
                $data['dateTimeReporteStart']=date_format($date, 'Y-m-d');
            }
            if($data['dateTimeReporteEnd']=='')
            {
                $campaign = Campaign::find($idCampaign);
                $date=date_create($campaign->dateTimeEnd);
                $data['dateTimeReporteEnd']=date_format($date, 'Y-m-d');
            }
        $inicio=str_replace('-', '', $data['dateTimeReporteStart']);//str_replace('-', '', "2017-05-07");//
        $final=str_replace('-', '', $data['dateTimeReporteEnd']);//str_replace('-', '', "2017-05-08");// $where = "";
       
        $V=DB::select(DB::raw('select * from "rptEvalRespCampOperEvaluacion"(' . $idCampaign .',\''.$inicio.'\',\''.$final .'\')'. $where .''));
        $V2=DB::select(DB::raw('select * from "rptEvalRespCampOperPreguntas"(' . $idCampaign .',\''.$inicio.'\',\''.$final .'\')'. $where .''));

        $Vsum=DB::select(DB::raw('SELECT  SUM(i) as i, SUM(lc) as lc, SUM(c) as c, SUM(nc) as nc, SUM(ni) as ni 
        FROM "rptEvalRespCampOperEvaluacion"(' . $idCampaign .',\''.$inicio.'\',\''.$final .'\')'. $where .''));

        $Vsum2=DB::select(DB::raw('SELECT "idQuestion", "idQuestionType", "dataAnswer", SUM(cant) as cant, MAX("order") as "order"
        FROM "rptEvalRespCampOperPreguntas"(' . $idCampaign .',\''.$inicio.'\',\''.$final .'\')'. $where .'GROUP BY "idQuestion", "idQuestionType", "dataAnswer" ORDER BY "order", "dataAnswer"'));
    
        //dd($V2);
        //dd($V);
        $data=array('v'=>$V, 'v2'=>$V2,'Vsum'=>$Vsum,'Vsum2'=>$Vsum2, 'idCampaign'=>$idCampaign, 'inicio'=>$inicio,'final'=>$final);
        //dd($data);
        return View::make("reporteOperador/show")->with('data',$data);
        }
        else
        {
            // En caso de error regresa a la acción create con los datos y los errores encontrados
        return Redirect::route('reporteOperador.create')->withInput()->withErrors($reporteOperador->errors);
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
       return View::make('reporteOperador/show');
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
