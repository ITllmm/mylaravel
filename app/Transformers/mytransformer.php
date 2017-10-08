<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class mytransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($book)
    {
        return [
            'id' => (int) $book['id'],
            'title' => $book['title'],
            'links' => [
                'rel' => 'myself',
                'uri' => '/books/'.$book['id'],
            ],
        ];
    }
}


