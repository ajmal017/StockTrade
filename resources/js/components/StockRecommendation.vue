<template>
    <div id="stock-recommendation" class="is-minheight-300">
        <p v-if="isLoading">
            {{ messages.loading }}
            <progress class="progress is-small" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
        </p>
        <template v-else>
            <span class="select">
                <select v-model.lazy="leaderboardFilter" @change="onFilterChanged">
                    <option value="all">All</option>
                    <option value="buy">Buy</option>
                    <option value="sell">Sell</option>
                    <option value="hold">Hold</option>
                </select>
            </span>
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Stock</th>
                        <th>Exc</th>
                        <th>Type</th>
                        <th>Best</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="Object.keys(entry).length==0">
                        <tr>
                            <td colspan="5" class="has-text-centered">
                                No Recommendations to show.
                            </td>
                        </tr>
                    </template>
                    <template v-for="e in entry">
                        <tr>
                            <td>
                                <modal btnText='<span class="icon is-small" title="View Historical Data"><i class="fa fa-line-chart"></i></span>' btnClass="card-header-icon" :minimal="true" :minheight="false">
                                    <stock-history-chart :symbol="e.symbol"></stock-history-chart>
                                </modal>
                            </td>
                            <td>{{ e.symbol }}</td>
                            <td>{{ e.issuer }}</td>
                            <td>{{ e.type }}</td>
                            <td>
                                <template v-if="e.recommendation === 'BUY' || e.recommendation === 'STRONG_BUY' ">
                                    <modal btnText="BUY" btnClass="is-link" :minimal="true" :minheight="false">
                                        <template slot="header">Buy {{ e.symbol }} at {{ e.currentPrice }}</template>
                                        <stock-buy
                                        :symbol="e.symbol"
                                        :name="e.name"
                                        :price="e.currentPrice"
                                        ></stock-buy>
                                    </modal>
                                </template>
                                <template v-else-if="e.recommendation === 'SELL' ">
                                    <modal btnText="SELL" btnClass="is-link" :minimal="true" :minheight="false">
                                        <template slot="header">Sell {{ e.symbol }} at {{ e.currentPrice }}</template>
                                        <stock-sell
                                        :symbol="e.symbol"
                                        :name="e.name"
                                        :price="e.currentPrice"
                                        ></stock-sell>
                                    </modal>
                                </template>
                                <template v-else>{{ e.recommendation }}</template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </template>
    </div>
</template>

<script>
import Axios from 'axios';
import StockBuy from './StockBuy.vue';
import StockSell from './StockSell.vue';
import StockHistoricalChart from './StockHistoricalChart.vue';
import Modal from './utils/Modal.vue';
import Events from './Events.js';

export default {
    data: function(){
        return {
            leaderboardFilter: 'all',
            isLoading: true,
            loadingPercent : 0,
            progressInterval : null,
            api : {
                getLeaderboard : hostname + '/api/v1/stocks/recommendation'
            },
            entry : {},
            messages : {
                loading : 'Getting Recommendations'
            },
        }
    },
    components: {
        'modal' : Modal,
        'stock-buy' : StockBuy,
        'stock-sell' : StockSell,
        'stock-history-chart' : StockHistoricalChart,
    },
    methods: {
        onFilterChanged(event){
            this.leaderboardFilter = event.target.value;
            this.getLeaderboard();
        },
        getLeaderboard(){
            var self = this;
            self.isLoadingFailed = false;
            self.isLoading = true;
            self.loadingPercent = 0;

            self.progressInterval = setInterval(function(){
                self.loadingPercent += 1;
                if(self.loadingPercent>100) self.loadingPercent = 0;
            },50);

            Axios.get(self.api.getLeaderboard,{ params : { filter : self.leaderboardFilter } }).then(function(response){
                self.entry = response.data.entry;
                self.isLoading = false;
                self.loadingPercent = 0;
                Events.$emit('updateFrameheight');
            }).catch(function (error) {
                self.messages.loading = 'Error: Failed getting watchlist';
                self.isLoadingFailed = true;
            });
        },
    },
    created: function(){

        var self = this;
        this.getLeaderboard();
    }
}
</script>

<style lang="css">
</style>
