<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent as Agent;
class NewsController extends Controller
{
    
    public function index(Request $request)
    {
        //获取当前栏目
        $key = 'Category_'.$request->id;
        $category = Cache::get($key);
        if(!$category)
        {
            $category = Category::findOrFail($request->id);
            Cache::put($key, $category, 604800);
        }
        $Agent = new Agent();
        $pageList = Article::where('post_type', $request->id)->paginate($Agent->isMobile()?8:15);
        $pageList->everypage = 15;
        if($Agent->isMobile())
        {
            $pageList->everypage = 8;
            return view('index.mobile.news', compact('pageList', 'category'));
        }
        return view('index.news', compact('pageList', 'category'));
    }
    
    public function detail(Request $request)
    {
        $Agent = new Agent();
        $info = Article::findOrFail($request->id);
        if($Agent->isMobile())
        {
            return view('index.mobile.detail', compact('info'));
        }
        return view('index.detail', compact('info'));
    }
}


