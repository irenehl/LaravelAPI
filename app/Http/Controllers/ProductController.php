<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Http\Request;
use Validator;
use Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductModel::paginate();
    }

    public function paginate(Request $request) {
        try {
            return ProductModel::paginate($request->query('limit'));
        }
        catch(Exception $e) {
            return [
                'message' => 'Something went wrong'
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku' => 'required',
            'name' => 'required',
            'stock' => 'required',
            'price' => 'required'
        ]);

        if($v->fails())
            return $v->errors();
        else
            return ProductModel::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ProductModel::find($id);
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
        $product = ProductModel::find($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = ProductModel::destroy($id);
        return $product;
    }

    /**
     * Searchs the specified resource from storage.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function search($sku)
    {
        return ProductModel::where('sku', $sku)->get();
    }

    public function searchName($name)
    {
        return ProductModel::where('name', 'like', '%'.$name.'%')->get();
    }
}
