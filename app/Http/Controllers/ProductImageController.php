<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImageCreateRequest;
use App\Http\Requests\ProductImageDeleteRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Response;
use Error;

class ProductImageController extends Controller
{

    private const PRODUCT_IMAGES_FOLDER = "MINIBACKOFFICE/PRODUCT_IMAGES";
    private const MAX_NUMBER_OF_IMAGES = 3;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductImageCreateRequest $productImageCreateRequest)
    {
        $data = $productImageCreateRequest->validated();

        $images = $productImageCreateRequest->file('images');

        if (!is_array($images)) {
            $error = new Error("You need to send an array of images", Response::HTTP_BAD_REQUEST);
            return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        if (count($images) > self::MAX_NUMBER_OF_IMAGES) {
            $error = new Error("Max number of images to send is " . self::MAX_NUMBER_OF_IMAGES, Response::HTTP_BAD_REQUEST);
            return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $product = Product::find($data["product_id"]);

        foreach ($images as $key => $image) {
            $realPath = $image->getRealPath();

            try {
                $uploadedFileUrl = Cloudinary::upload($realPath, [
                    "folder" => self::PRODUCT_IMAGES_FOLDER,
                    "transformation" => [
                        ["width" => 500, "height" => 400, "crop" => "scale"]
                    ]
                ])->getPublicId();

                $product->images()->create(['url' => $uploadedFileUrl]);
            } catch (\Exception $ex) {
                continue;
                /* $error = new Error($ex->getMessage(), Response::HTTP_UNAUTHORIZED);
                return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED); */
            }
        }

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductImageDeleteRequest $productImageDeleteRequest)
    {
        $data = $productImageDeleteRequest->validated();

        $images_ids = $data["images_ids"];

        $product = Product::find($data["product_id"]);

        $product_images_to_delete = $product->images->filter(function ($productImage) use ($images_ids) {
            return in_array($productImage->id, $images_ids);
        });

        if (count($product_images_to_delete) === 0) {
            $error = new Error("Nothing to delete", Response::HTTP_BAD_REQUEST);
            return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        foreach ($product_images_to_delete as $key => $image) {
            try {
                Cloudinary::destroy($image->url);

                $product->images()->find($image->id)->delete();
            } catch (\Exception $ex) {
                continue;
                /* $error = new Error($ex->getMessage(), Response::HTTP_UNAUTHORIZED);
                return (new ErrorResource($error))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED); */
            }
        }

        return new ProductResource($product->fresh());
    }
}
