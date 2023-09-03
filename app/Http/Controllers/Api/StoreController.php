<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Store;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::all();
        return response()->json($stores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $store = new Store();
        $store->name = $request->name;
        $store->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = Store::findOrFail($id);
        return $store->name;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $store = Store::findOrFail($id);
        $store->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
    }

    public function cepLa()
    {
        $cep = '31744503';
        $client = new Client();
        $request = $client->get("https://viacep.com.br/ws/{$cep}/json/");
        $response = $request->getBody()->getContents();
        return $response;
    }

    public function viaCep()
    {
        $cep = '31744503';

        $client = new Client();
        $request = $client->get("https://viacep.com.br/ws/{$cep}/json/");
        $response = $request->getBody()->getContents();
        $value = json_decode($response);
        echo $value->bairro;
    }
}
