<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaterialType;
use App\Http\Controllers\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class SampleController extends Controller
{
    /**
     * manage samples
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all the samples
        $samples = DB::table('sample')
        ->orderBy('storage_date', 'desc')
        ->get();

        // load the view and pass the samples
        return view('sampleList')
            ->with ('samples', $samples)
        ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // get MaterialTypes
        $material = MaterialType::get('type_of_material');

        // get requests
        $tank_pos   = $request->tank_pos;
        $con_pos    = $request->con_pos;
        $tube_pos = $request->tube_pos;
        $sample_pos = $request->sample_pos;

        // load the create form (app/resources/views/samples.blade.php)
        return view('newSamples')
            ->with('tank_pos', $tank_pos)
            ->with('con_pos', $con_pos)
            ->with('tube_pos', $tube_pos)
            ->with('sample_pos', $sample_pos)
            ->with('material', $material)
        ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        // get request
        $tank_pos    = $request->tank_pos;
        $con_pos     = $request->con_pos;
        $tube_pos    = $request->tube_pos;
        $sample_pos  = $request->sample_pos;
        $identifier  = $request->identifier;
        $materialtyp = $request->materialtyp;
        $commentary  = $request->commentary;

        // store
        $sample = new sample;
        
        $sample->identifier         = $identifier;
        $sample->pos_tank_nr        = $tank_pos;
        $sample->pos_insert         = $con_pos;
        $sample->pos_tube           = $tube_pos;
        $sample->pos_smpl           = $sample_pos;
        $sample->responsible_person = Auth::user()->email;
        $sample->type_of_material   = $materialtyp;
        $sample->commentary         = $commentary;

        if ($this->checkIfIdentifierExists($identifier, 'sample') ||
            $this->checkIfIdentifierExists($identifier, 'shipped_sample') ||
            $this->checkIfIdentifierExists($identifier, 'removed_sample')) {
            
            $lastCheckedTable = $this->getLastCheckedTable();
            return redirect('/')
            ->with('error', __('messages.Probe konnte nicht angelegt werden, da eine Probe mit dem Identifier ":identifier" bereits in der Tabelle ":table" existiert.', ['identifier' => $identifier, 'table' => $lastCheckedTable]));
        }

        $sample->save();
        return redirect('/');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete
        $sample = Sample::where('id','=', $id );
        echo $sample->get();
        $sample->delete();

        // redirect
        return view('home');
    }


    private $lastCheckedTable;

    private function checkIfIdentifierExists($identifier, $table)
    {
        $exists = DB::table($table)->where('identifier', $identifier)->exists();
        if ($exists) {
            $this->lastCheckedTable = $table;
        }
        return $exists;
    }

    public function getLastCheckedTable() {
        return $this->lastCheckedTable;
    }

}
