<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Product_condition;
use Illuminate\Http\JsonResponse;
use App\Services\ImageUploadService;
use App\Http\Requests\StoreProductRequest;
use App\Services\Product\ProductServiceImpl;

class ProductController extends Controller
{
    private const PRODUCT_CREATED_SUCCESSFULLY = 'Product created successfully';
    const PRODUCT_UPDATED_SUCCESSFULLY = 'Product updated successfully.';
    private const DEFAULT_IMAGE_URL = '/images/default-image.jpg';
    protected ProductServiceImpl $productServiceImpl;

    public function __construct(ProductServiceImpl $productServiceImpl)
    {
        $this->productServiceImpl = $productServiceImpl;
    }

    public function index(): JsonResponse
    {
        // $product = Product_condition::with('products')->get();
        // return response()->json( $product);
         // Eager load related tables: product_condition and categories
         $products = Product::with(['product_con', 'categories'])->get();

         return response()->json([
             'status' => 200,
             'products' => $products
         ], 200);

        
        $product = product_condition::all();
        $result = ['status' => 200];
        try {
            $result['products'] = $this->productServiceImpl->getProducts();
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    public function getProductsByCategory(int $categoryId): JsonResponse
    {
        $result = ['status' => 200];
        $products = $this->productServiceImpl->getProductsByCategory($categoryId);
        $result['products'] = $products;
        return response()->json($result, $result['status']);
    }

    /**
     * @throws Exception
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        // $product_condition= product_condition::get();
        // return response()->json($abhi);
        // return response()->json($request);
        $product = $this->createProduct($request);
        // dd($request);
        $category_id = $request->input('category_id');
        $createdProduct = $this->productServiceImpl
            ->addProduct($product, $category_id);
        return response()->json([
            'message' => self::PRODUCT_CREATED_SUCCESSFULLY,
            'createdProduct' => $createdProduct
        ], 201);
    }

    protected function createProduct(storeProductRequest $request): Product
    {
        // console.log("product1",$request);
        if ($request->hasFile('image')) {
            $path = ImageUploadService::uploadImage($request->file('image'));
            $image = $path;
        } else {
            $image = config('app.url') . self::DEFAULT_IMAGE_URL;
        }
        // Calculate discounted price if a discount is provided
        $price = $request->input('price');
        $discount = $request->input('discount', 0); // Default to 0 if not provided

        if ($discount > 0) {
            $discountedPrice = $price - ($price * ($discount / 100));
        } else {
        $discountedPrice = $price;
        }

        return new Product([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $discountedPrice, // Store discounted price
            'image' => $image,
            'discount' => $discount,     // Store discount value
            'product_condition_id' => $request->input('product_condition_id'),
        ]);
     }

     public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);  // Find the product by ID
            // dd($product);
            // Check if the request has an image and upload the new one
            if ($request->hasFile('image')) {
                $path = ImageUploadService::uploadImage($request->file('image'));
                $product->image = $path; // Update image
            }

            // Update product fields
            $product->name = $request->input('name', $product->name);
            $product->description = $request->input('description', $product->description);
            $product->price = $request->input('price', $product->price);
            $product->discount = $request->input('discount', $product->discount);
            $product->product_condition_id = $request->input('product_condition_id', $product->product_condition_id);
            // $category_id = $request->input('category_id');
            

            // Calculate discounted price if a discount is provided
            if ($product->discount > 0) {
                $product->price = $product->price - ($product->price * ($product->discount / 100));
            }

            $product->save(); 
            // dd($product); // Save the updated product to the database
            $product->categories()->sync([$request->category_id]);

            return response()->json([
                'status' => 200,
                'message' => self::PRODUCT_UPDATED_SUCCESSFULLY,
                'product' => $product,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update the product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


     public function getProductById($id): JsonResponse
{
    try {
        $product = Product::with('categories')->findOrFail($id);  // Eager load categories
        return response()->json([
            'status' => 200,
            'product' => $product,
            'categories' => $product->categories // Return associated categories
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'status' => 404,
            'message' => 'Product not found!',
            'error' => $e->getMessage()
        ], 404);
    }
}

public function destroy(Product $product): JsonResponse
{
    $product->delete();
    return response()->json(null, 204);
}

}
