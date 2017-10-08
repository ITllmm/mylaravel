<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use App\Transformers\mytransformer;

class FractalController extends Controller
{

    public function test()
    {
           $book = ['id' => 3,'title' => 'hogchild']; //Item data

            $books = [
                ['id' => 1, 'title' => 'hogfather', 'characters' => ['height' => 30, 'weight' => '40']],
                ['id' => 2, 'title' => 'hogmother', 'characters' => ['height' => 22, 'weight' => '33']]
            ]; //Collection data

            $manager = new Manager();
            $resource1 = new Item($book,new BookTransformer());
            $resource2 = new Collection($books,new BookTransformer());

            //$array = $manager->createData($resource)->toArray();

             echo $manager->createData($resource1)->toJson();
             echo '<br/>';
             echo $manager->createData($resource2)->toJson();

             echo '<br/>';
             echo  fractal($books, new BookTransformer())->respond(403, [
                'a-header' => 'a value',
                'another-header' => 'another value',
            ]); //php artisan vendor:publish --provider="Spatie\Fractal\FractalServiceProvider"  [notice:$book error due to 'fractal']

            echo '<br/>';
            echo fractal($books,new mytransformer()) -> respond(); //php artisan make:transformer mytransformer
    }
}

// TransFormer
class BookTransformer extends TransformerAbstract
{
    public function transform($book)
    {
        return [
            'id' => (int) $book['id'],
            'title' => $book['title'],
            'links' => [
                'rel' => 'self',
                'uri' => '/books/'.$book['id'],
            ],
        ];
    }
}
