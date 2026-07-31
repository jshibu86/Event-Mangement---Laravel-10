<?php
namespace App\Http\Controllers;
use App\Models\Game; use App\Models\Program; use Illuminate\Http\Request; use Illuminate\Support\Str;
class MasterController extends Controller {
    private function model(string $type): string { return $type === 'games' ? Game::class : Program::class; }
    public function index(string $type) { $model=$this->model($type); return view('masters.index', compact('type','model')); }
    public function create(string $type) { return view('masters.form', ['type'=>$type,'record'=>null]); }
    public function store(Request $r,string $type) { $data=$r->validate(['name'=>'required|max:255','description'=>'nullable','category'=>'nullable|max:100','min_players'=>'nullable|integer|min:1','max_players'=>'nullable|integer|min:1','video_urls'=>'nullable|string']); $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5)); $data['status']=$r->boolean('status'); $data['created_by']=auth()->id(); $data['video_urls']=collect(preg_split('/\r?\n/', $data['video_urls'] ?? ''))->filter()->values(); ($this->model($type))::create($data); return redirect()->route('masters.index',$type)->with('success',ucfirst(rtrim($type,'s')).' created.'); }
    public function edit(string $type,int $id) { $record=($this->model($type))::findOrFail($id); return view('masters.form',compact('type','record')); }
    public function update(Request $r,string $type,int $id) { $record=($this->model($type))::findOrFail($id); $data=$r->validate(['name'=>'required|max:255','description'=>'nullable','category'=>'nullable|max:100','min_players'=>'nullable|integer|min:1','max_players'=>'nullable|integer|min:1','video_urls'=>'nullable|string']); $data['status']=$r->boolean('status'); $data['video_urls']=collect(preg_split('/\r?\n/', $data['video_urls'] ?? ''))->filter()->values(); $record->update($data); return redirect()->route('masters.index',$type)->with('success','Saved.'); }
    public function destroy(string $type,int $id) { ($this->model($type))::findOrFail($id)->delete(); return back()->with('success','Archived.'); }
}
