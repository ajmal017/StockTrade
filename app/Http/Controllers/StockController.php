<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;
use App\Watchlist;
use App\History;
use Curl;
use Auth;
use Carbon\Carbon;

class StockController extends Controller
{

    public $options = [
        'a' => 'Ask Price',
        'b' => 'Bid Price',
        'o' => 'Open',
        'p' => 'Previous Close',
        'c1' => 'Change',
        'p2' => 'Percent Change',
        'g' => 'Day Low',
        'h' => 'Day High',
        's6' => 'Revenue',
        'k' => 'Year High',
        'j' => 'Year Low',
        'j5' => 'Year Low Change',
        'k4' => 'Year High Change',
        'j6' => 'Year Low Change Percent',
        'k5' => 'Year High Change Percent',
        'j1' => 'Market Capitalization',
        'f6' => 'Float Shares',
        'n' => 'Name',
        's' => 'Symbol',
        'x' => 'Stock Exchange',
        'j2' => 'Outstanding Shares',
        'v' => 'Volume',
        'a5' => 'Ask Size',
        'b6' => 'Bid Size',
        'k3' => 'Last Trade Size',
        'a2' => 'Average Daily Volume',
        'e' => 'Earnings Per Share'
    ];
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        //
    }


    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        //
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        //
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($symbol,$options = "n,s,a,b,o,p,v,c1,p2,g,h,s6,k,j,j5,k4,j6,k5,j1,x,f6,j2,a5,b6,k3,a2,e")
    {
        // $symbol = urlencode($symbol);

        // Check if Symbol already on database, if yes, check last updated time
        $isExist = Stock::where('symbol',$symbol)->first();

        $fetchData = true;
        $updateData = false;



        if(!empty($isExist)){

            $MinutesAgo = Carbon::now()->diffInMinutes($isExist->updated_at);
            $threshold = 60; // minutes

            $fetchData = false;

            if($MinutesAgo>$threshold){
                $updateData = true;
                $fetchData = true;
            }

        }


        if($fetchData){

            // If symbol not yet in database, or if saved data was outdated already, proceed fetch

            $options = explode(",",$options);
            $range = 'max';

            if($updateData)
            $range = '1d';

            $options = [
                'range' => $range,
                'interval' => '1d',
                'indicators' => 'quote',
                'includeTimestamps' => 'true',
                'corsDomain' => 'finance.yahoo.com'
            ];
            $url = 'https://query1.finance.yahoo.com/v7/finance/chart/' . $symbol . '?' . http_build_query($options);

            $response = Curl::to($url)
            ->get();

            // $response = explode("\n", $response);
            $data = [];
            if(!empty($response)){
                $response = json_decode($response);

                if(isset($response->chart) && $response->chart->error==null){
                    $result = $response->chart->result;

                    if(is_array($result))
                    $result = array_shift($result);

                    $timestamps = array_keys((array)$result);

                    $data = array_filter((array)$result->meta,function($key,$value){
                        if(in_array($key,[
                            'currentTradingPeriod',
                            'dataGranularity',
                            'validRanges'
                            ])) return false;
                            return true;
                        },ARRAY_FILTER_USE_BOTH);
                    }

                    // Save Historical Data
                    $history = [];

                    foreach ((array)$result->timestamp as $key => $unix) {
                        History::updateOrCreate(
                            [
                                'symbol' => $symbol,
                                'timestamp' => Carbon::createFromTimestamp($unix)->toDateTimeString()
                            ],
                            [
                                'high' => $result->indicators->quote[0]->high[$key],
                                'open' => $result->indicators->quote[0]->open[$key],
                                'close' => $result->indicators->quote[0]->close[$key],
                                'low' => $result->indicators->quote[0]->low[$key],
                                'volume' => $result->indicators->quote[0]->volume[$key],
                                'unadjhigh' => $result->indicators->unadjquote[0]->unadjhigh[$key],
                                'unadjlow' => $result->indicators->unadjquote[0]->unadjlow[$key],
                                'unadjopen' => $result->indicators->unadjquote[0]->unadjopen[$key],
                                'unadjclose' => $result->indicators->unadjquote[0]->unadjclose[$key]
                            ]
                        );
                    }

                }

                if(empty($data)){
                    return response()->json([
                        'error' => 'Failed to get data',
                        'message' => 'Could not fetch statistics for ' . $symbol
                    ]);
                }

                $baseURLQuote = "https://query2.finance.yahoo.com/v7/finance/quote";

                $infoStats = Curl::to($baseURLQuote)
                ->withData([
                    'formatted' => 'true',
                    'lang' => 'en-US',
                    'region' => 'US',
                    'symbols' => $symbol,
                    'fields' => implode(',',[
                        'longName',
                        'shortName',
                        'underlyingSymbol',
                        'underlyingExchangeSymbol',
                        'headSymbolAsString',
                    ]),
                    'corsDomain' => 'finance.yahoo.com',
                ])
                ->asJson()
                ->get();

                if($infoStats && $infoStats->quoteResponse->error==null){
                    $infoStats = (array)$infoStats->quoteResponse->result[0];
                }else{
                    $infoStats = [];
                    return response()->json([
                        'error' => 'Failed to get data',
                        'message' => 'Could not fetch info for ' . $symbol
                    ]);
                }


                $baseURLQuote = "https://query2.finance.yahoo.com/v10/finance/quoteSummary/" .$symbol;

                $response = Curl::to($baseURLQuote)
                ->withData([
                    'formatted' => 'true',
                    'crumb' => 'HLn18oo0lxL',
                    'lang' => 'en-US',
                    'region' => 'US',
                    'modules' => implode(',',[
                        'summaryProfile',
                        'detail',
                        'assetProfile',
                        'financialData',
                        'defaultKeyStatistics'
                    ]),
                    'corsDomain' => 'finance.yahoo.com',
                ])
                ->asJson()
                ->get();

                if($response && isset($response->quoteSummary->result[0]->defaultKeyStatistics)){
                    $profile = $response->quoteSummary->result[0]->assetProfile;
                    $detailedStats = [
                        'defaultKeyStatistics' => $response->quoteSummary->result[0]->defaultKeyStatistics,
                        'financialData' => $response->quoteSummary->result[0]->financialData,
                        'info' => $infoStats
                    ];
                }else{
                    return response()->json([
                        'error' => 'Failed to get data',
                        'message' => 'Could not fetch profile for ' . $symbol
                    ]);
                }

                $details = $data;


                $isWatched = Watchlist::where([ 'user_id' => Auth::id(), 'stock_symbol' => $detailedStats['info']['symbol'] ])->exists();

                $isExist = Stock::where('symbol',$detailedStats['info']['symbol'])->first();
                if(!empty($isExist)){
                    $updateData = true;
                }
                // If everythings ok, check if symbol already on DB
                if($updateData){
                    Stock::where('symbol',$detailedStats['info']['symbol'])->update([
                        'issuer' => $details['exchangeName'],
                        'type' => $details['instrumentType'],
                        'statistics' => json_encode($detailedStats),
                        'profile' => json_encode($profile),
                    ]);
                }else{
                    Stock::create([
                        'symbol' => $detailedStats['info']['symbol'],
                        'issuer' => $details['exchangeName'],
                        'name' => $infoStats['shortName'],
                        'type' => $details['instrumentType'],
                        'statistics' => json_encode($detailedStats),
                        'profile' => json_encode($profile),
                    ]);
                }

                return response()->json([
                    'profile' => $profile,
                    'details' => $detailedStats,
                    'watched' => $isWatched,
                    'status' => 'OK'
                ]);
            }else{

                $statistics = json_decode($isExist->statistics);
                $profile = json_decode($isExist->profile);
                // Check if symbol in watchlist
                $isWatched = Watchlist::where([ 'user_id' => Auth::id(), 'stock_symbol' => $statistics->info->symbol ])->exists();

                // Return response from DB

                if(!empty($isExist)){
                    return response()->json([
                        'profile' => $profile,
                        'details' => $statistics,
                        'watched' => $isWatched,
                        'status' => 'OK'
                    ]);
                }

                return response()->json([
                    'status' => 'FAILED'
                ]);
            }

        }

        /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function edit($id)
        {
            //
        }

        /**
        * Update the specified resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function update(Request $request, $id)
        {
            //
        }

        /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */
        public function destroy($id)
        {
            //
        }


        /**
        * Display a listing of stock symbols.
        *
        * @return \Illuminate\Http\Response
        */
        public function search(Request $request)
        {
            $term = $request->input('q');
            if(empty($term)) return response()->json([]);

            $response = Curl::to('http://d.yimg.com/autoc.finance.yahoo.com/autoc?region=us&lang=en&query='.$term)
            ->asJson()
            ->get();

            if(!empty($response->ResultSet->Result)) return response()->json($response->ResultSet->Result);
            return response()->json([]);
        }

    }
