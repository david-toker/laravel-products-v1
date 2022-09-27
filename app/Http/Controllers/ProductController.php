<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function addProduct(Request $request)
    {
        $formFields = $request->validate(([
            'name'=>'required',
            'price'=>'required',
            'description'=>'required',
        ]));

        if($request->hasFile('file')) {
            $formFields['file_path'] = $request->file('file')->store('products');
        }

        return Product::create($formFields);

    }

    function list()
    {
        // return Product::all();
        return Product::paginate(3);
    }

    function delete($id) {
        $result = Product::where('id', $id)->delete();
        if($result) {
            return ['result'=>'product has been deleted!'];
        } else {
            return ['result'=>'operation failed'];
        }
    }

    function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $result = Product::whereIn('id',explode(",",$ids))->delete();
        if ($result) {
            return ['success'=>"Products Deleted successfully."];
        } else {
            return ['result'=>'operation failed'];
        }
    }

    function getProduct($id)
    {
        return Product::find($id);
    }

    function updateProduct($id, Request $request)
    {
        $product = Product::find($id);
        $product->update($request->all());
        if ($request->file('file')) {
            $product->file_path = $request->file('file')->store('products');
        }
        $product->save();

        return $product;
    }

    function search($key)
    {
        return Product::where("name", "Like", "%$key%")->get();
    }
}
