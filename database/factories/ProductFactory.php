<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {

    $image = $faker->randomElement([
        "https://imgsa.baidu.com/forum/w%3D580/sign=6567962cc311728b302d8c2af8fdc3b3/499be950352ac65ca45f7fcef5f2b21192138ad5.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=4fb83d52a7ec8a13141a57e8c7029157/ec21f8dcd100baa1d5855e064910b912c9fc2ef9.jpg",
        "https://aod-image-material.cdn.bcebos.com/0/pic/e5e60b46150588c998fe386395a779db.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=d8545d09b8003af34dbadc68052bc619/88f1ab64034f78f019b127f377310a55b3191c10.jpg",
        "https://imgsa.baidu.com/forum/w%3D580%3B/sign=192ebfb23b2ac65c6705667bcbc9b011/9a504fc2d5628535c674b1f39eef76c6a6ef634f.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=a8e7e19d3712b31bc76ccd21b6193674/3a480923dd54564e493299e3bdde9c82d0584fb5.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=6f3bb0f38818367aad897fd51e728b68/e144d109b3de9c820e3284556281800a18d843b5.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=ae87669985d4b31cf03c94b3b7d7276f/bfa36c81800a19d85854f9df3dfa828ba71e46b5.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=588fa6aba34bd11304cdb73a6aaea488/e84c4fc2d562853575c790f99eef76c6a6ef63db.jpg",
        "https://imgsa.baidu.com/forum/w%3D580/sign=3c4388a69c58d109c4e3a9bae159ccd0/7aeb90529822720eede4092275cb0a46f31fabd9.jpg",
    ]);

    $category = \App\Models\Category::query()->where('is_directory',false)->inRandomOrder()->first();

    return [
        'title'       => $faker->word,
        'description' => $faker->sentence,
        'image'       => $image,
        'on_sale'     => true,
        'rating'      => $faker->numberBetween(0,5),
        'sold_count'  => 0,
        'review_count'=> 0,
        'price'       => 0,
        //如果数据库中没有类目则$category 为null ，同时$category_id也设为null
        'category_id' => $category ? $category->id : null,
    ];
});
