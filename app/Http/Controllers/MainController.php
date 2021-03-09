<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;

class MainController extends Controller
{
    public function scrape(Request $request) {

        //Get url param for scraping
        $url = $request->get('url');
        $listingCardArray = [];
        for ($loop=1; $loop < 11; $loop++) { 
            $url = "https://dealmarkaz.pk/mobile-phones_karachi-c356521/".$loop;
        //Init Guzzle
            $client = new Client();

            //Get request
            $response = $client->request(
                'GET',
                $url
            );

            $response_status_code = $response->getStatusCode();
            $html = $response->getBody()->getContents();
            if($response_status_code==200){
                $dom = HtmlDomParser::str_get_html( $html );

                $listingCards = $dom->find('li[class="listing-card"]');
                dd($listingCards);
                $i = 1;
                
                foreach ($listingCards as $listCard){
                    // if($i == 1) {
                        $listingCardArray[$loop][$i]['thumb'] = trim($listCard->find('a[class="listing-thumb"] > img',0)->attr['data-src']);
                        $listingCardArray[$loop][$i]['title'] = trim($listCard->find('div[class="basicinfo"] > a',0)->text());
                        $listingCardArray[$loop][$i]['short_descrpition'] = trim($listCard->find('div[class="basicinfo"] > p',0)->text());
                        $listingCardArray[$loop][$i]['price'] = trim($listCard->find('div[class="currency-value"]',0)->text());
                        $listingCardArray[$loop][$i]['location'] = trim($listCard->find('span[class="location"]',0)->text());
                        $listingCardArray[$loop][$i]['detail'] = $this->detailPage(trim($listCard->find('div[class="basicinfo"] > a',0)->attr['href']));
                    // }
                    
                    $i++;
                }
            }
        }
        return $listingCardArray;
    }

    public function detailPage($url) {
        
        //Init Guzzle
        $client = new Client();

        //Get request
        $response = $client->request(
            'GET',
            $url
        );
        $response_status_code = $response->getStatusCode();
        $html = $response->getBody()->getContents();

        if($response_status_code==200){
            $dom = HtmlDomParser::str_get_html( $html );

            $itemContent = $dom->find('div[id="item-content"]', 0);
            $photoGalleries = $itemContent->find('div[class="item-photo-gallery-thumbs"] > div[class="swiper-slide"]');
            $images = [];
            $thumbs = [];
            foreach ($photoGalleries as $key => $image) {
                $thumbHref = $image->find('img[class="swiper-lazy"]',0)->attr['data-src'];
                $thumbs[$key] = $thumbHref;
                $images[$key] = str_replace('_thumbnail', '', $thumbHref);
            }

            return  [
                'images' => $images,
                'thumbs' => $thumbs,
                'description' => trim($itemContent->find('div[id="description"] > p', 0)->text()) . "<br>" . trim($itemContent->find('div[id="description"] > p', 1)->text()),
            ];

             // let item = $('#item-content');
             //                          item.find('#description').find('.ad-info-2').remove();
             //        let descrpition = item.find('#description').html().trim();
             //        let images = [];
             //            item.find('.item-photo-gallery').find('.swiper-slide').each(function(i,el){
             //                let imgSrc = $(el).find('img').attr('src');
             //                let imgDataSrc = $(el).find('img').attr('data-src');
             //                let img = imgSrc != null ? imgSrc : imgDataSrc;
             //                images.push(img);
             //            });

             //        let thumbs = [];
             //            item.find('.item-photo-gallery-thumbs').find('.swiper-slide').each(function(i,el){
             //                let imgSrc = $(el).find('img').attr('src');
             //                let imgDataSrc = $(el).find('img').attr('data-src');
             //                let img = imgSrc != null ? imgSrc : imgDataSrc;
             //                thumbs.push(img);
             //            });
             //        results  = {
             //            table: $(item).find('.item-attribute-list').text().trim(),
             //            descrpition: descrpition,
             //            images: images.toString(),
             //            thumbs: thumbs.toString(),
             //        };         
        }
    }
}
