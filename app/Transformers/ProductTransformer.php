<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id' =>$product->id,
            'category_id' => $product->category_id,
            'title' => $product->title,
            'price' => $product->price,
            'image' => $product->image,
            'rating' => $product->rating,
            'sold_count' => $product->sold_count,
            'review_count' => $product->review_count
        ];
    }

}
