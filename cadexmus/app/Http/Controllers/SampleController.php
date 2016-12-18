<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Sample;

class SampleController extends Controller
{
    public function index()
    {
		$samples = Sample::orderBy('nom')->get();

        //return view('sample.index',compact("samples"));
        //return view('sample.index',['samples' => $samples]);
        //return view('sample.index')->with('samples',$samples);
        return view('sample.index')->withSamples($samples);
    }

    public function create()
    {
        return view('sample.create');
    }

    // méthode appelée par la route get sample/{sample}
    public function show($id)
    {
        return view('sample.show')->withSample(Sample::find($id));
    }


    public function store(Request $request)
    {
        // redirect automatique à la page précédente si la validation échoue
        $this->validate($request, [
            'url' => 'mimetypes:audio/mpeg,audio/x-wav',
            'nom' => 'required',
        ]);

        if ($request->url->isValid()) {
            $url = $request->url->storeAs("samples/users/$request->type", $request->nom . "_" . uniqid() .".". $request->url->extension());
            $s = Sample::create(array_merge(['url'=>$url], $request->only('nom', 'type')));
            return redirect()->route("sample.show",$s->id);
        }
    }

    public function filter($pattern){
        $patterns = explode(" ", $pattern);

        // requête de barbare pour un filtrage efficace
        // le nom OU le type contient arg1 ET le nom ou le type contient arg2 ET etc...
        // select * from `samples` where ((`nom` like '%arg1%' or `type` like '%arg1%') and (`nom` like '%arg2%' or `type` like '%arg2%')) order by `nom` asc
        $samples = Sample::where(function($q) use ($patterns) {
            foreach ($patterns as $keyword) {
                $q->where(function($q) use ($keyword){
                    $q->where('nom', 'like', "%$keyword%")
                        ->orWhere('type', 'like', "%$keyword%");
                })->get();
            }
        })->orderBy('nom')->get();

        return view('sample.list')->withSamples($samples);
    }

    public function listAll(){
        $samples = Sample::orderBy('nom')->get();
        return view('sample.list')->withSamples($samples);
    }
}
