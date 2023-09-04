<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Store;
use GuzzleHttp\Client;
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

        if (!$request->name) {
            echo "Dados não salvos, envie novamente";
        }
        $store->name = $request->name;
        $store->save();
        $this->storeAddress($request, $store->id);
        echo "dados salvos com sucesso";
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
            $data +=["postal_code_masked" => $this->cepMask('#####-###', $address->postal_code)];
            $data +=["street_number" => $address->street_number];
            $data +=["state" => $address->state];
            $data +=["city" => $address->city];
            $data +=["sublocality" => $address->sublocality];
            $data +=["street" => $address->street];
            $data +=["complement" => $address->complement];           
            
            return $data;
        }
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

    public function cepLa(string $cep)
    {
        $client = new Client();
        $request = $client->get("https://viacep.com.br/ws/{$cep}/json/");
        $response = $request->getBody()->getContents();        
        if(!$response){
            $this->viaCep($cep);
        }
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
        } else return $value;
    }

    public function cepMask($mask, $cep)
    {
        $cep = str_replace(" ", "", $cep);

        for ($i = 0; $i < strlen($cep); $i++) {
            $mask[strpos($mask, "#")] = $cep[$i];
        }
        return $mask;
    }
}
