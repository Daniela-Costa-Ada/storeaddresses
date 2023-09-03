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
        //FUNCIONA
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $store = new Store();

        if (!$request->name) {
            echo "Dados não salvos, envie novamente";
        }
        $store->name = $request->name;
        $store->save();
        $this->storeAddress($request, $store->id);
        echo "dados salvos com sucesso";
        //FUNCIONA
    }

    public function storeAddress(Request $request, $id)
    {
        $address = new Address();
        $address->postal_code = $request->postal_code;
        $address->street_number = $request->street_number;
        $address->complement = $request->complement;
        $dataAddress = $this->cepLa($address->postal_code);
        $dataAddress = json_decode($dataAddress);
        $address->state = $dataAddress->uf;
        $address->city = $dataAddress->localidade;
        $address->sublocality = $dataAddress->bairro;
        $address->street = $dataAddress->logradouro;
        $address->foreign_id = $id;
        $address->save();
        //FUNCIONA
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $store = Store::findOrFail($id);
        if ($store) {
            $data = ["name" => $store->name];
        }
        $address = $store->address()->first();
        if ($address) {
            $data += ["postal_code" => $address->postal_code];
            return $data;
        }
    } //FUNCIONA

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

    public function cepLa(string $cep)
    {
        $client = new Client();
        $request = $client->get("https://viacep.com.br/ws/{$cep}/json/");
        $response = $request->getBody()->getContents();       
        return $response;
    }

    public function viaCep(string $cep)
    {

        $client = new Client();
        $request = $client->get("https://viacep.com.br/ws/{$cep}/json/");
        $response = $request->getBody()->getContents();
        $value = json_decode($response);
        if ($value->erro) {
            echo "Cep nao encontrado";
        }else return $value;
    }
}
