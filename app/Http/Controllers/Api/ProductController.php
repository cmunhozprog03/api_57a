<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
class ProductController extends Controller
{
    /**
     * @var Product
     */
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        $products = $this->product;

        if($request->has('conditions'))
        {
            $expressions = explode(';', $request->get('conditions'));

            foreach ($expressions as $e)
            {
                $exp = explode('=', $e);
                $products = $products->where($exp[0], $exp['1']);
            }


        }

        if($request->has('fields'))
        {
            $fields = $request->get('fields');
            $products = $products->selectRaw($fields);
        }

        //return response()->json($products);
        return new ProductCollection($products->paginate(10));
    }

    public function show($id)
    {
        $product = $this->product->find($id);

        //return response()->json($products);
        return new ProductResource($product);
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $product = $this->product->create($data);
        return response()->json($product);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $product = $this->product->find($data['id']);
        $product->update($data);

        return response()->json($product);

    }

    public function delete($id)
    {
        $product = $this->product->find($id);
        $product->delete();

        return response()->json(['data' => ['msg' => 'Produto removido com sucesso']]);
    }
}
