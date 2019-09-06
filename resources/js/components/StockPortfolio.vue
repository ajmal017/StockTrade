<template>
    <div id="tab-portfolio">
        <div class="section content">
            <div class="container">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            Performance Overview
                        </p>
                        <a class="card-header-icon">
                            <span class="icon is-small" @click="onRetryFetch">
                                <i class="fa fa-refresh"></i>
                            </span>
                        </a>
                    </header>
                    <div class="card-content">
                        <p v-if="isLoading">
                            {{ messages.loading }}
                            <progress class="progress is-small" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
                        </p>
                        <p v-else>
                            <nav class="level">
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Account Value</p>
                                        <p class="title">{{ portfolio.accountValueFmt }}</p>
                                    </div>
                                </div>
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Cash Value</p>
                                        <p class="title">{{ portfolio.cashValueFmt }}</p>
                                    </div>
                                </div>
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Total Shares</p>
                                        <p class="title">{{ portfolio.totalShares }}</p>
                                    </div>
                                </div>
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Total Gains</p>
                                        <p class="title">{{ portfolio.totalGainsFmt }}</p>
                                    </div>
                                </div>
                                <div class="level-item has-text-centered">
                                    <div>
                                        <p class="heading">Total Gain %</p>
                                        <p class="title">{{ portfolio.totalGainsPercent }}</p>
                                    </div>
                                </div>
                            </nav>
                        </p>
                    </div>
                </div>
                <div v-if="!isLoading" class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            My Stocks
                        </p>
                    </header>
                    <div class="card-content">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Stock</th>
                                    <th>Current Value</th>
                                    <th>Shares Owned</th>
                                    <th>Total Purchased Price</th>
                                    <th>Total Current Price</th>
                                    <th>Total Gain/Loss</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="stocks.length==0">
                                    <tr>
                                        <td colspan="10" class="has-text-centered">No Purchased Stocks yet </td>
                                    </tr>
                                </template>
                                <template v-for="stock in stocks">
                                    <tr>
                                        <td>
                                            <a class="button is-link" @click.prevent="toggleActive(stock.symbol)">
                                                <span class="icon is-small">
                                                    <i class="fa" :class="{ 'fa-angle-up' : active==stock.symbol, 'fa-angle-down' : active!=stock.symbol }" ></i>
                                                </span>
                                            </a>
                                        </td>
                                        <td>
                                            <modal btnText="Buy" btnClass="is-link" :minimal="true" :minheight="false">
                                                <template slot="header">Buy {{ stock.symbol }} at {{ stock.currentPrice }}</template>
                                                <stock-buy
                                                :symbol="stock.symbol"
                                                :name="stock.name"
                                                :price="stock.currentPrice"
                                                ></stock-buy>
                                            </modal>
                                        </td>
                                        <td>
                                            <modal btnText="Sell" btnClass="is-link" :minimal="true" :minheight="false">
                                                <template slot="header">Sell {{ stock.symbol }} at {{ stock.currentPrice }}</template>
                                                <stock-sell
                                                :symbol="stock.symbol"
                                                :name="stock.name"
                                                :price="stock.currentPrice"
                                                ></stock-sell>
                                            </modal>
                                        </td>
                                        <td>
                                            <modal btnText='<span class="icon is-small" title="View Historical Data"><i class="fa fa-line-chart"></i></span>' btnClass="card-header-icon" :minimal="true" :minheight="false">
                                                <stock-history-chart :symbol="stock.symbol"></stock-history-chart>
                                            </modal>
                                        </td>
                                        <td>{{ stock.symbol }}</td>
                                        <td>${{ stock.currentPrice }}</td>
                                        <td>{{ stock.qty }}</td>
                                        <td>${{ stock.purchasedPriceTotal }}</td>
                                        <td>${{ stock.currentPriceTotal }}</td>
                                        <td>${{ stock.gain }} ( {{ stock.gainPercent }}% )</td>
                                    </tr>

                                    <template v-if="active==stock.symbol">
                                        <tr class="is-narrow">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>Transaction Date</td>
                                            <td>Purchase Price</td>
                                            <td>Quantity</td>
                                            <td>Total Purchased Price</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <template v-for="log in stock.history">
                                            <tr class="is-narrow">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td> {{ log.type }} </td>
                                                <td> <abbr :title="log.updated_at">{{ log.purchasedTimeAgo }}</abbr> </td>
                                                <td> {{ log.priceFormatted }} </td>
                                                <td> {{ log.qtyFormatted }} </td>
                                                <td> {{ log.totalFormatted }} </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </template>
                                        <tr class="is-narrow">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><strong>Total</strong></td>
                                            <td></td>
                                            <td><strong>{{ stock.qty }}</strong></td>
                                            <td><strong>${{ stock.purchasedPriceTotal }}</strong></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </template>

                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="columns">
                    <div class="column is-full-mobile is-full-tablet is-two-thirds-desktop">
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    Top Performing Stocks
                                </p>
                            </header>
                            <div class="card-content is-overflow">
                                <stock-leaderboard></stock-leaderboard>
                            </div>
                        </div>
                    </div>
                    <div class="column is-full-mobile is-full-tablet is-one-third-desktop">
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    Latest Stock Recommendation
                                </p>
                            </header>
                            <div class="card-content is-overflow">
                                <stock-recommendation></stock-recommendation>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';
import Modal from './utils/Modal.vue';
import StockBuy from './StockBuy.vue';
import StockSell from './StockSell.vue';
import StockHistoricalChart from './StockHistoricalChart.vue';
import StockLeaderboard from './StockLeaderboard.vue';
import StockRecommendation from './StockRecommendation.vue';
import Events from './Events.js';

export default {
    data : function(){
        return {
            isLoading: true,
            active : null,
            messages : {
                loading : 'Getting portfolio details...'
            },
            loadingPercent : 0,
            progressInterval : null,
            api : {
                getPorfolioStats : hostname + '/api/v1/user/portfolio'
            },
            stocks : null,
            portfolio : {
                totalShares : 0
            }
        }
    },
    components: {
        'modal' : Modal,
        'stock-buy' : StockBuy,
        'stock-sell' : StockSell,
        'stock-leaderboard' : StockLeaderboard,
        'stock-recommendation' : StockRecommendation,
        'stock-history-chart' : StockHistoricalChart,
    },
    methods: {
        getPorfolioStats(){
            var self = this;
            self.isLoadingFailed = false;
            self.isLoading = true;
            self.loadingPercent = 0;

            self.progressInterval = setInterval(function(){
                self.loadingPercent += 1;
                if(self.loadingPercent>100) self.loadingPercent = 0;
            },50);

            Axios.get(self.api.getPorfolioStats).then(function(response){
                self.stocks = response.data.stocks;
                self.portfolio = response.data.portfolio;
                self.isLoading = false;
                self.loadingPercent = 0;
            }).catch(function (error) {
                self.messages.loading = 'Error: Failed getting watchlist';
                self.isLoadingFailed = true;
            });
        },
        onRetryFetch(){
            this.getPorfolioStats();
        },
        toggleActive(symbol){
            if(this.active==symbol){
                this.active = null;
            }else{
                this.active = symbol;
            }
        }
    },
    created: function(){
        var self = this;
        this.getPorfolioStats();

        Events.$on('buyingSuccess',function(symbol){
            self.getPorfolioStats();
        });
        Events.$on('sellingSuccess',function(symbol){
            self.getPorfolioStats();
        });
    }
}
</script>

<style lang="css">
</style>
