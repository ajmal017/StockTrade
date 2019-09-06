<template>
    <div id="stocktrade">
        <div id="tab-watchlist">
            <div class="section content">
                <div class="container">

                    <div class="card">
                        <header class="card-header">
                            <p class="card-header-title">
                                Favorite Stocks
                            </p>
                        </header>
                        <div class="card-content">
                            <div class="content"  v-if="!isLoading">
                                <div v-if="Object.keys(stocks).length" id="stockitem-list" class="columns is-multiline">
                                    <stock-item v-for="(stock,i) in stocks" :key="stock.id" :symbol="stock.symbol" :details="stock.data"></stock-item>
                                </div>
                                <div v-else class="has-text-centered">
                                    <blockquote>
                                        <h1><span class="icon is-large">
                                            <i class="fa fa-meh-o"></i>
                                        </span><br>No Followed Stocks yet.</h1>Sad to hear that you're not following any stocks yet. <br>Start getting updates from your favorite stocks by clicking the button below.
                                    </blockquote>
                                </div>
                            </div>
                            <div v-else class="content has-text-centered">
                                <p>
                                    Fetching your favorite stocks...
                                    <progress class="progress is-small" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
                                </p>
                            </div>
                        </div>
                        <footer class="card-footer">
                            <div class="column is-one-third is-offset-one-third">
                                <modal btnText="Add Symbol" btnClass="card-footer-item is-fullwidth">
                                    <template slot="header">Add Symbol to Watchlist</template>
                                    <label class="label">Search Symbol or Stock Name</label>
                                    <p class="control">
                                        <autocomplete
                                        url="/api/v1/stock/search"
                                        anchor="symbol"
                                        label="name"
                                        placeholder="e.g. GOOGL"
                                        class-name="input"
                                        :onSelect="getSymbolData"
                                        ></autocomplete>
                                    </p>
                                    <preview></preview>
                                </modal>
                            </div>
                        </footer>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import Autocomplete from 'vue2-autocomplete-js';
import Events from './Events.js';
import Preview from './StockTradeWatchListPreview.vue';
import StockItems from './StockWatchListItems.vue';
import Axios from 'axios';
import Modal from './utils/Modal.vue';
import Tabs from './utils/Tabs.vue';
import Tab from './utils/Tab.vue';

var autocomplete = Vue.extend({
    mixins: [Autocomplete],
    mounted: function(){
        var self = this;
        self.$el.children[0].className = 'input autocomplete-input';
        Events.$on('retrySymbolDataFetch',function(){
            self.type = self.$el.children[0].value;
            self.onSelect({
                symbol : self.$el.children[0].value
            });
        });
    }
});

export default {
    name: 'stocktrade-watchlist',
    data: function(){
        return {
            isLoading: true,
            messages : {
                loading : 'Getting favorite quotes...'
            },
            loadingPercent : 0,
            progressInterval : null,
            stocks : [],
            api : {
                getWatchlist : {
                    url : hostname + "/api/v1/user/watchlist",
                    response : null
                },
                getStockData : {
                    url : hostname + "/api/v1/stock/data/",
                    response : null
                },
                logoutAccount : {
                    url : hostname + "/api/v1/user/logout",
                    response : null
                }
            }
        }
    },
    components: {
        autocomplete,
        'modal' : Modal,
        'tabs' : Tabs,
        'tab' : Tab,
        'preview' : Preview,
        'stock-item' : StockItems
    },
    methods: {

        getWatchList() {
            // Fetch Symbols from Database
            var self = this;
            self.progressInterval = setInterval(function(){
                self.loadingPercent += 1;
                if(self.loadingPercent>100) self.loadingPercent = 0;
            },50);
            Axios.get(self.api.getWatchlist.url).then(function(response){
                self.stocks = response.data.watchlist;
                self.isLoading = false;
            }).catch(function (error) {
                self.messages.loading = 'Error: Failed getting watchlist';
                self.api.getWatchlist.response = [];
            });
        },
        getSymbolData(result){
            if(result.symbol!=""){
                // Fetch Symbols from Database
                var self = this;
                Events.$emit('symbolDataLoading',result.symbol);
                Axios.get(self.api.getStockData.url + result.symbol).then(function(response){
                    if(typeof response.data.error !== "undefined"){
                        Events.$emit('symbolDataLoadFailed',result.symbol);
                        return false;
                    }else{
                        Events.$emit('symbolDataLoaded',response.data);
                        self.api.getStockData.response = response;
                        return true;
                    }
                }).catch(function (error) {
                    Events.$emit('symbolDataLoadFailed',result.symbol);
                });
            }
        }
    },
    created: function(){

        var self = this;
        this.getWatchList();

        Events.$on('symbolWatchlistAdded',function(){
            self.getWatchList();
        });
        Events.$on('symbolWatchlistRemoved',function(){
            self.getWatchList();
        });


    }
}
</script>
