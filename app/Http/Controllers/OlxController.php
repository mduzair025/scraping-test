<?php

namespace App\Http\Controllers;

use App\Traits\GetHtml;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;

class OlxController extends Controller
{
	use GetHtml;
    public function index(Request $request, $url= null)
    {
    	$url = $url ?? 'https://www.olx.com.pk/karachi_g4060695/mobile-phones_c1453';
    	$dom =  $this->set($url);

    	$items = $dom->find('li[class="EIR5N"]');
    	$data = [];
    	foreach (array_slice($items,7) as $key => $item) {
    		$data[$key]['thumb'] = $item->find('img',0)->attr['src'];
    		$data[$key]['price'] = trim($item->find('span[class="_89yzn"]',0)->text());
    		$data[$key]['title'] = trim($item->find('span[class="_2tW1I"]',0)->text());
    		$data[$key]['city_area'] = trim($item->find('span[class="tjgMj"]',0)->text());
    	}
    	dd($data);

    }
}
