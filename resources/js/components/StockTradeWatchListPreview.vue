<template>
    <div id="stockchart">
        <div v-if="isLoading">
            {{ messages.loading }}
            <progress class="progress is-small" v-bind:class="{ 'is-danger' : isLoadingFailed }" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
            <a v-if="isLoadingFailed" class="button is-link" @click.prevent="onRetryFetch">Try Again</a>
        </div>
        <div v-if="!isLoading && isSearched">
            <div class="content">
                <div class="has-text-centered">
                    <h1 class="title is-2">{{ stockData.details.info['symbol'] }}</h1>
                    <h5 class="subtitle">{{ stockData.details.info['shortName'] }}</h5>
                </div>
                <div class="columns">
                    <div v-if="!isWatched" class="column is-half is-offset-one-quarter">
                        <a v-if="!isAdding" class="button block is-fullwidth is-primary is-medium" :class=" { 'is-danger' : isAdded == 'error' , 'is-disabled' : isAdded } " @click="addToWatchlist(stockData.details.info['symbol'])" >
                            <span  class="icon is-small">
                                <i class="fa" :class=" { 'fa-check' : isAdded === true, 'fa-exclamation-triangle' : isAdded == 'error', 'fa-heart' : !isAdded } "></i>
                            </span>
                            <span v-if="isAdded === true ">Added to Watchlist</span>
                            <span v-else-if="isAdded == 'error'">Adding Failed</span>
                            <span v-else>Add to Watchlist</span>
                        </a>
                        <a v-else class="button block is-fullwidth is-primary is-medium is-loading"></a>
                    </div>
                    <div v-else class="column is-half is-offset-one-quarter">
                        <a v-if="!isRemoving" class="button block is-fullwidth is-danger is-medium" @click="removeFromWatchlist(stockData.details.info['symbol'])" >
                            <span  class="icon is-small">
                                <i class="fa" :class=" { 'fa-ban' : isAdded === true, 'fa-exclamation-triangle' : isAdded == 'error', 'fa-heart' : !isAdded } "></i>
                            </span>
                            <span v-if="isRemoved === true ">Stock Unfollowed</span>
                            <span v-else-if="isRemoved == 'error'">Unfollow Failed</span>
                            <span v-else>Unfollow Stock</span>
                        </a>
                        <a v-else class="button block is-fullwidth is-danger is-medium is-loading"></a>
                    </div>
                </div>
                <div class="columns has-text-centered">
                    <div class="column">
                        <h3 class="title">{{ stockData.details.financialData.currentPrice.fmt }}</h3>
                        <h6 class="subtitle is-6">Current Price</h6>
                    </div>
                    <div class="column">
                        <h3 class="title">{{ stockData.details.financialData.targetLowPrice.fmt }}</h3>
                        <h6 class="subtitle is-6">Target Low</h6>
                    </div>
                    <div class="column">
                        <h3 class="title">{{ stockData.details.financialData.targetHighPrice.fmt }}</h3>
                        <h6 class="subtitle is-6">Target High</h6>
                    </div>
                    <div class="column">
                        <h3 class="title">{{ stockData.details.financialData.totalRevenue.fmt }}</h3>
                        <h6 class="subtitle is-6">Total Revenue</h6>
                    </div>
                </div>
                <p>{{ stockData.profile.longBusinessSummary }}</p>

                <tabs tabclass="">
                    <tab name="Profile" :selected="true">
                        <table class="table">
                            <tbody>
                                <tr :class="{ 'hidden' : isIgnored(type,stats)}" v-for="(stats, type) in stockData.details.info"><td>{{ getStatName(type) }}</td><td v-html="displayStatData(type,stats)"></td></tr>
                            </tbody>
                        </table>
                    </tab>
                    <tab name="Key Statistics">
                        <table class="table">
                            <tbody>
                                <tr :class="{ 'hidden' : isIgnored(type,stats)}" v-for="(stats, type) in stockData.details.defaultKeyStatistics"><td>{{ getStatName(type) }}</td><td v-html="displayStatData(type,stats)"></td></tr>
                            </tbody>
                        </table>
                    </tab>
                    <tab name="Financial Data">
                        <table class="table">
                            <tbody>
                                <tr :class="{ 'hidden' : isIgnored(type,stats)}" v-for="(stats, type) in stockData.details.financialData"><td>{{ getStatName(type) }}</td><td v-html="displayStatData(type,stats)"></td></tr>
                            </tbody>
                        </table>
                    </tab>
                    <tab name="More Details">
                        <table class="table">
                            <tbody>
                                <tr :class="{ 'hidden' : isIgnored(type,stats)}" v-for="(stats, type) in stockData.profile"><td>{{ getStatName(type) }}</td><td v-html="displayStatData(type,stats)"></td></tr>
                            </tbody>
                        </table>
                    </tab>
                </tabs>
            </div>
        </div>
    </div>
</template>

<script>
import Events from './Events.js';
import Axios from 'axios';
import Tabs from './utils/Tabs.vue';
import Tab from './utils/Tab.vue';

export default {
    name : 'stockchart',
    data: function(){
        return {
            isSearched : false,
            isLoading: false,
            isLoadingFailed : false,
            isWatched : false,
            progressInterval : null,
            messages : {
                loading : 'Initializing quote data...',
            },
            loadingPercent : 0,
            stockData : [],
            stockWatched : false,
            api : {
                addToWatchlist : hostname + "/api/v1/user/watchlist/add/",
                removeFromWatchlist : hostname + "/api/v1/user/watchlist/remove/"
            },
            isAdding : false,
            isAdded : false,
            isRemoving : false,
            isRemoved : false,
        }
    },
    components: {
        'tabs' : Tabs,
        'tab' : Tab,
    },
    computed:{
    },
    methods: {
        isIgnored(type,stats){

            var ignores = [
                "maxAge",
                "exchange",
                "exchangeTimezoneShortName",
                "gmtOffSetMilliseconds",
                "language",
                "sourceInterval",
                "longBusinessSummary",
                "maxAge",
                "industrySymbol",
            ];
            if(ignores.indexOf(type) !== -1){
                return true;
            }

            if(stats==null || (typeof stats == 'object' && Object.keys(stats).length==0)) return true;

            return false;
        },
        getStatName(type){
            var statLabels = {
                'fundFamily' : 'Fund Family',
                'annualHoldingsTurnover' : 'Annual Holdings Turnover',
                'SandP52WeekChange' : 'SandP 52-Week Change',
                '52WeekChange' : '52-Week Change',
                'legalType' : 'Legal Type',
                'floatShares' : 'Floating Shares',
                'enterpriseToEbitda' : 'Enterprise To Ebitda',
                'enterpriseToRevenue' : 'Enterprise To Revenue',
                'earningsQuarterlyGrowth' : 'Earnings Quarterly Growth',
                'fiveYearAverageReturn' : '5-Yr Ave Return',
                'enterpriseValue' : 'Enterprise Value',
                'beta3Year' : 'Beta 3-Yr',
                'beta' : 'Beta',
                'annualReportExpenseRatio' : 'Annual Report Expense Ratio',
                'bookValue' : 'Book Value',
                'category' : 'Category',
                'mostRecentQuarter' : 'Most Recent Quarter',
                'maxAge' : 'Max Age',
                'lastCapGain' : 'Last Capital Gain',
                'lastDividendValue' : 'Last Dividend Value',
                'forwardPE' : 'Forward PE',
                'lastSplitDate' : 'Last Split Data',
                'lastSplitFactor' : 'Last Split Factor',
                'forwardEps' : 'Forward EPS',
                'fundInceptionDate' : 'Fund Inception Date',
                'heldPercentInsiders' : 'Held % Insiders',
                'lastFiscalYearEnd' : 'Last Fiscal Year End',
                'heldPercentInstitutions' : 'Held % Institutions',
                'pegRatio' : 'peg Ratio',
                'morningStarRiskRating' : 'Morning Star Risk Rating',
                'morningStarOverallRating' : 'Morning Star Overall Rating',
                'totalAssets' : 'Total Assets',
                'priceToBook' : 'Price to Book',
                'nextFiscalYearEnd' : 'Next Fiscal Year End',
                'netIncomeToCommon' : 'Next Income to Common',
                'sharesShort' : 'Short Shares',
                'priceToSalesTrailing12Months' : 'Price to Sales Trailing 12mos',
                'profitMargins' : 'Profit Margins',
                'revenueQuarterlyGrowth' : 'Revenue Quarterly Growth',
                'sharesOutstanding' : 'Outstanding Shares',
                'sharesShortPriorMonth' : 'Short Prior Month Shares',
                'shortPercentOfFloat' : 'Short % Floating Shares',
                'shortRatio' : 'Short Ratio',
                'threeYearAverageReturn' : '3-Yr Ave Return',
                'trailingEps' : 'Trailing EPS',
                'yield' : 'Yield',
                'ytdReturn' : 'Yield Return',
                "currentPrice" : "Current Price",
                "targetHighPrice" : "Target High Price",
                "targetLowPrice" : "Target Low Price",
                "targetMeanPrice" : "Target Mean Price",
                "targetMedianPrice" : "Target Median Price",
                "recommendationMean" : "Recommendation Mean",
                "recommendationKey" : "Recommendation Key",
                "numberOfAnalystOpinions" : "Number of Analyst Opinions",
                "totalCash" : "Total Cash",
                "totalCashPerShare" : "Total Cash Per Share",
                "ebitda" : "Ebitda",
                "totalDebt" : "Total Debt",
                "quickRatio" : "Quick Ratio",
                "currentRatio" : "Current Ratio",
                "totalRevenue" : "Total Revenue",
                "debtToEquity" : "Debt to Equity",
                "revenuePerShare" : "Revenue per Share",
                "returnOnAssets" : "Return on Assets",
                "returnOnEquity" : "Return on Equity",
                "grossProfits" : "Gross Profits",
                "freeCashflow" : "Free Cash Flow",
                "operatingCashflow" : "Operating Cash Flow",
                "earningsGrowth" : "Earnings Growth",
                "revenueGrowth" : "Revenue Growth",
                "grossMargins" : "Gross Margins",
                "ebitdaMargins" : "Ebitda Margins",
                "operatingMargins" : "Operating Margins",
                "profitMargins" : "Profit Margins",
                "quoteType":"Quote Type",
                "exchangeTimezoneName":"Exchange Timezone",
                "fullExchangeName":"Full Exchange Name",
                "longName":"Long Name",
                "market":"Market",
                "marketState":"Market Status",
                "shortName":"Short Name",
                "symbol":"Symbol",
                "address1":"Address",
                "fullTimeEmployees": "Fulltime Employess",
                "country":"Country",
                "compensationAsOfEpochDate": "Compensation Since",
                "governanceEpochDate": "Governance Start",
                "industry":"Industry",
                "compensationRisk":"Compensation Risk",
                "city":"City",
                "boardRisk":"Board Risk",
                "companyOfficers":"Company Officers",
                "auditRisk":"Audit Risk",
                "overallRisk":"Overall Risk",
                "phone":"Phone",
                "sector":"Sector",
                "shareHolderRightsRisk":"Shareholder Rights Risk",
                "state":"State",
                "website":"Website",
                "zip":"Zip",
            };

            return statLabels.hasOwnProperty(type) ? statLabels[type] : type;
        },
        padZero(str,len, c){
            var s = ''+str, c = c || '0';
            while(s.length< len) s = c + s;
            return s;
        },
        timeConverter(UNIX_timestamp){
            var a = new Date(UNIX_timestamp * 1000);
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            var year = a.getFullYear();
            var month = months[a.getMonth()];
            var date = a.getDate();
            var hour = a.getHours();
            var min = a.getMinutes();
            var sec = a.getSeconds();
            var time = this.padZero(date,2) + ' ' + month + ' ' + year + ' ' + this.padZero(hour,2) + ':' + this.padZero(min,2) + ':' + this.padZero(sec,2) ;
            return time;
        },
        displayStatData(type,stats){
            var value = '-';

            if(!stats) return '-';

            if(typeof stats === 'object' && stats.length==0){
                return '-';
            }

            switch (type) {
                case 'companyOfficers':
                value = '<ul>';
                stats.forEach(stat => { value+= '<li><span>'+stat.name+'</span><br><small>'+stat.title+'</small></li>'; });
                value += '</ul>'
                break;
                case 'compensationAsOfEpochDate':
                case 'governanceEpochDate':
                value = this.timeConverter(stats);
                break;
                case 'compensationRisk':
                case 'boardRisk':
                case 'auditRisk':
                case 'overallRisk':
                case 'shareHolderRightsRisk':
                var part = (parseInt(stats)/10)*100;
                value = '<progress class="progress is-small" value="'+part+'" max="100">'+part+'%</progress>';
                break;
                default:
                if(typeof stats === 'object'){
                    if(stats.hasOwnProperty('fmt')){
                        value = stats.fmt;
                        if(stats.hasOwnProperty('raw')){
                            value = '<span title="'+stats.raw+'">'+stats.fmt+'</span>';
                        }
                    }else{
                        value = stats.raw;
                    }
                }else if(typeof stats === 'array'){
                    value = '<ul>';
                    stats.forEach(stat => { value+= '<li><span>'+stat.toString()+'</span></li>'; });
                    value += '</ul>'
                }else{
                    value = stats;
                }
                break;
            }

            return value;
        },
        addToWatchlist(symbol){
            var self = this;
            self.isAdding = true;
            self.isAdded = false;

            Axios.get(self.api.addToWatchlist + symbol).then(function(response){
                self.isAdding = false;
                if(response.status == 200 && response.data.status == 'OK'){
                    self.isAdded = true;
                    self.isWatched = true;
                    self.isRemoving = false;
                    self.isRemoved = false;
                    Events.$emit('symbolWatchlistAdded');
                }
                else{
                    self.isAdded = 'error';
                    setTimeout(function(){
                        self.isAdded = false;
                    },3000);
                }
            });
        },
        removeFromWatchlist(symbol){
            var self = this;
            self.isRemoving = true;
            self.isRemoved = false;

            Axios.get(self.api.removeFromWatchlist + symbol).then(function(response){
                self.isAdding = false;
                if(response.status == 200 && response.data.status == 'OK'){
                    self.isAdded = false;
                    self.isWatched = false;
                    self.isRemoved = true;
                    self.isRemoving = true;
                    Events.$emit('symbolWatchlistRemoved');
                }
                else{
                    self.isRemoved = 'error';
                    setTimeout(function(){
                        self.isRemoved = false;
                    },3000);
                }
            });
        },
        onRetryFetch(){
            Events.$emit('retrySymbolDataFetch');
        }
    },
    created: function(){

        var self = this;

        Events.$on('symbolDataLoading',function(){
            self.messages.loading = "Initializing quote data...";
            self.isLoadingFailed = false;
            self.isLoading = true;
            self.isSearched = true;

            self.isAdded = false;
            self.isWatched = false;
            self.isRemoved = false;
            self.isRemoving = false;

            self.progressInterval = setInterval(function(){
                self.loadingPercent += 1;
                if(self.loadingPercent>100) self.loadingPercent = 0;
            },50);
        });

        Events.$on('symbolDataLoaded',function(data){
            clearInterval(self.progressInterval);
            self.loadingPercent = 100;
            self.isLoadingFailed = false;
            self.isLoading = false;
            self.stockData = data;
            self.isWatched = data.watched;
        });

        Events.$on('symbolDataLoadFailed',function(symbol){
            clearInterval(self.progressInterval);
            self.loadingPercent = 100;
            self.isLoading = true;
            self.isLoadingFailed = true;
            self.messages.loading = "Failed to fetch data for symbol " + symbol;
        });

    }
}
</script>
