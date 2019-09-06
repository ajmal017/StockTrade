<template>
    <div class="historical-chart">
        <div v-if="isLoading">
            {{ messages.loading }}
            <progress class="progress is-small" v-bind:class="{ 'is-danger' : isLoadingFailed }" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
            <a v-if="isLoadingFailed" class="button is-link" @click.prevent="onRetryFetch">Try Again</a>
        </div>
        <div v-else>
            <highstock :options="chartOptions"></highstock>
            <p>
                Last Updated : {{ lastUpdated }}
            </p>
        </div>
    </div>
</template>

<script>
import VueHighcharts from 'vue-highcharts';
import Highcharts from 'highcharts/highstock';

Vue.use(VueHighcharts, { Highcharts });

import Axios from 'axios';
export default {
    data : function(){
        return {
            stockhistory : {},
            chartOptions : {},
            isLoading : true,
            isLoadingFailed : false,
            progressInterval : null,
            lastUpdated : null,
            loadingPercent : 0,
            messages : {
                loading : 'Fetching Stock Historical Data ...',
            },
            api : {
                getStockHistory : hostname + "/api/v1/stock/history/"
            },
        };
    },
    props: {
        symbol : {
            required: true
        }
    },
    components: {
        // VueHighcharts,
        // Highcharts
    },
    methods : {
        getStockHistory(){
            var self = this;
            self.progressInterval = setInterval(function(){
                self.loadingPercent += 1;
                if(self.loadingPercent>100) self.loadingPercent = 0;
            },50);
            Axios.get(self.api.getStockHistory + self.symbol).then(function(response){
                if(response.data.history) self.prepareChart(response.data.history);
                self.lastUpdated = response.data.lastUpdated;
                self.isLoading = false;
            }).catch(function (error) {
                self.messages.loading = 'Error: Failed getting watchlist';
            });
        },
        onRetryFetch(){
            this.getStockHistory();
        },
        prepareChart(chartData){
            this.chartOptions = {
                title: {
                    text: this.symbol + ' Historical Data',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Source: Yahoo! Finance',
                    x: -20
                },
                yAxis: {
                    title: {
                        text: 'Source: Yahoo! Finance'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    valueSuffix: '',
                    valueDecimals: 2
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [
                    {
                        name : 'Open',
                        data : chartData.open,
                    },
                    {
                        name : 'High',
                        data : chartData.high,
                    },
                    {
                        name : 'Low',
                        data : chartData.low,
                    },
                    {
                        name : 'Close',
                        data : chartData.close,
                    }
                ],
            }
        }
    },
    created: function(){
        this.getStockHistory();
    }
}
</script>
<style lang="css">
#chartdiv {
    width: 100%;
    height: 500px;
}
</style>
