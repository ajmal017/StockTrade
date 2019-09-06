<template>
    <div id="stock-leaderboard" class="is-minheight-300">
        <p v-if="isLoading">
            {{ messages.loading }}
            <progress class="progress is-small" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
        </p>
        <template v-else>
        <span class="select">
            <select v-model.lazy="leaderboardFilter" @change="onFilterChanged">
                <option value="price">Price</option>
                <option value="revenue">Revenue</option>
                <option value="value">Enterprise Value</option>
                <option value="high">Target High</option>
                <option value="low">Target Low</option>
            </select>
        </span>
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Symbol</th>
                        <th>Name</th>
                        <th>Exchange</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="e in entry">
                        <tr>
                            <td>
                                <modal btnText='<span class="icon is-small" title="View Historical Data"><i class="fa fa-line-chart"></i></span>' btnClass="card-header-icon" :minimal="true" :minheight="false">
                                    <stock-history-chart :symbol="e.symbol"></stock-history-chart>
                                </modal>
                            </td>
                            <td>{{ e.symbol }}</td>
                            <td>{{ e.name }}</td>
                            <td>{{ e.issuer }}</td>
                            <td>{{ e.type }}</td>
                            <td>{{ e.value }}</td>
                            <td>
                                <modal btnText="Buy" btnClass="is-link" :minimal="true" :minheight="false">
                                    <template slot="header">Buy {{ e.symbol }} at {{ e.currentPrice }}</template>
                                    <stock-buy
                                    :symbol="e.symbol"
                                    :name="e.name"
                                    :price="e.currentPrice"
                                    ></stock-buy>
                                </modal>
                            </td>
                            <td>
                                <modal btnText="Sell" btnClass="is-link" :minimal="true" :minheight="false">
                                    <template slot="header">Sell {{ e.symbol }} at {{ e.currentPrice }}</template>
                                    <stock-sell
                                    :symbol="e.symbol"
                                    :name="e.name"
                                    :price="e.currentPrice"
                                    ></stock-sell>
                                </modal>
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

export default {
    data: function(){
        return {
            leaderboardFilter: 'revenue',
            isLoading: true,
            loadingPercent : 0,
            progressInterval : null,
            api : {
                getLeaderboard : hostname + '/api/v1/stocks/top'
            },
            entry : {},
            messages : {
                loading : 'Getting portfolio details...'
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
