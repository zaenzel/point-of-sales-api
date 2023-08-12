<?php

namespace App\Http\Controllers;

use App\Http\Resources\PosResource;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller
{
    public function index()
    {
        $foods = Food::latest()->paginate(20);

        return new PosResource(true, 'List Data Foods', $foods);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/foods', $image->hashName());

        $food = Food::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $image->hashName()
        ]);

        return new PosResource(true, 'Data food berhasil di tambahkan', $food);
    }

    public function show($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return new PosResource(false, 'Detail data tidak ditemukan', 404);
        }

        return new PosResource(true, 'Detail data food', $food);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string',
            'price' => 'required|integer',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $food = Food::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {
            
             //upload image
            $image = $request->file('image');
            $image->storeAs('public/foods', $image->hashName());

             //delete old image
            Storage::delete('public/foods/'.basename($food->image));

             //update post with new image
            $food->update([
                'name' => $request->name,
                'price' => $request->price,
                'image' => $image->hashName()
            ]);

        } else {

             //update post without image
            $food->update([
                'name' => $request->name,
                'price' => $request->price,
            ]);
        }

        return new PosResource(true, 'Data food berhasil di update', $food);
    }

    public function destroy($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return new PosResource(false, 'Data food tidak ditemukan', 404);
        }

        Storage::delete('public/foods'.basename($food->image));

        $food->delete();

        return new PosResource(true, 'Data food berhasil dihapus', null);
    }
}
