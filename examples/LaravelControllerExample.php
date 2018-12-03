<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DonatelloZa\RakePlus\RakePlus;

class ArticleController extends Controller
{
    /**
     * Store a new article post.
     *
     * @param  Request  $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $text = $request->input('article_text');
        $keywords = RakePlus::create($text)->keywords();

        // Store the array of keywords to a database table etc.
        // ....

        // Handle rest of the request...
    }
}
