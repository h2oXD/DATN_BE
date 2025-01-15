<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBaseController extends Controller
{
    /**
     * @var Model|string $model
     */

    public $model;
    public $pathView;

    public $fieldImage;

    public $folderImage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->model->newQuery()->paginate();

        return view($this->pathView. __FUNCTION__, compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->pathView. __FUNCTION__);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validateStore($request);

        $model = new $this->model;

        $model->fill($request->except([$this->fieldImage]));
        
         if ($request->hasFile($this->fieldImage)) {
            $tmpPath = Storage::put($this->folderImage,  $request->{$this->fieldImage});
            // tương đương $request->{$this->fieldImage}->$request->avatar
            $model->{$this->fieldImage} = 'storage/' .  $tmpPath;
         }

        $model->save();

        return back()->with('success', 'thao tác thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = $this->model->newQuery()->findOrFail($id);
        return view($this->pathView. __FUNCTION__, compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $model = $this->model->newQuery()->findOrFail($id);
        return view($this->pathView. __FUNCTION__, compact('model'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validateUpdate($request);

        $model = $this->model->newQuery()->findOrFail($id);

        $model->fill($request->except([$this->fieldImage]));
        
         if ($request->hasFile($this->fieldImage)) {
            $oldImage = $model->{$this->fieldImage};

            $tmpPath = Storage::put($this->folderImage,  $request->{$this->fieldImage});
            
            $model->{$this->fieldImage} = 'storage/' .  $tmpPath;
         }

        $model->save();

        if ($request->hasFile($this->fieldImage)) {
            $oldImage = str_replace('storage/', '', $oldImage);
            Storage::delete($oldImage);
        }

        return back()->with('success', 'thao tác thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = $this->model->newQuery()->findOrFail($id);

        $model->delete();

        if (Storage::exists($model->{$this->fieldImage})) {
            $Image = str_replace('storage/', '', $model->{$this->fieldImage});
            Storage::delete($Image);
        }
    }

    public function import(){
        
    }


    public function export(){

    }

    public function validateStore(Request $request) {
        
    }


    public function validateUpdate(Request $request) {

    }
}
