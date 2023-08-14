<?php

namespace App\Http\Controllers;

use App\Http\Resources\PosResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->paginate(10);

        return new PosResource(true, 'List Data Transaksi', $transactions);
    }

    public function show($id)
    {
        $transaction = Transaction::with('detailTransaction.food')->find($id);

        if (!$transaction) {
            return new PosResource(false, 'Detail data tidak ditemukan', 404);
        }

        return new PosResource(true, 'Detail data transaksi', $transaction);
    }

    public function store(Request $request)
    {
        $rules = [
            'total_price' => 'required|integer',
            'total_cash' => 'required|integer',
            'total_change' => 'integer',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.subtotal' => 'required|integer|min:0',
        ];

        $customMessages = [
            'required' => 'Field :attribute harus diisi.',
            'integer' => 'Field :attribute harus berupa angka.',
            'min' => 'Field :attribute minimal :min.',
            'items.min' => 'Minimal satu item harus ditambahkan.',
            'items.*.min' => 'Field :attribute minimal :min.',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules, $customMessages);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $transaction = Transaction::create($data);
        
        foreach($data['items'] as $foodItem){
            $transaction->detailTransaction()->create($foodItem);
        }

        return new PosResource(true, 'Data transaksi berhasil di tambahkan', $transaction);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'total_price'=>'required|integer',
            'total_cash'=>'required|integer',
            'total_change'=>'required|integer',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $transaction = Transaction::find($id);

        if(!$transaction){
            return new PosResource(false, 'transaksi tidak di temukan', null);
        }

        $transaction->update($data);

        return new PosResource(true, 'Data transaksi berhasil di update', $transaction);
    }

    public function destroy($id){
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return new PosResource(false, 'Data transaksi tidak di temukan', null);
        }

        $transaction->delete();

        return new PosResource(true, 'Data berhasil dihapus', null);
    }
}
