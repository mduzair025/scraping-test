<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScrapeController extends Controller
{

    private $states = [];
    private $bd_states = [];

    public function get_data() {
        $client = new Client();
        
        $page = $client->request('GET', 'https://dealmarkaz.pk/mobile-phones');
        // echo '<pre>';
        // print_r($page);
        // $total = $page->filter('.maincounter-number')->text();

        $page->filter('.listing-card')->each(function($item) {
            array_push($this->states, $item);
        });

        dd($this->states);
        $page_bd = $client->request('GET', 'https://www.worldometers.info/coronavirus/country/bangladesh/');

        $page_bd->filter('.maincounter-number')->each(function($item) {
            array_push($this->bd_states, $item->text());
        });

        $result = $this->returnResult();

        return response($result, 200);
        
    }

    private function returnResult() {
        $output = [];
        $output['total_affected'] = $this->states[0];
        $output['total_death'] = $this->states[1];
        $output['total_reacovered'] = $this->states[2];

        $output['total_affected_bd'] = $this->bd_states[0];
        $output['total_death_bd'] = $this->bd_states[1];
        $output['total_reacovered_bd'] = $this->bd_states[2];

        return $output;
    }
}
