<template lang="html">
    <div id="stocktrade-transactions">
        <div class="section">
            <div class="container">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            Latest Transactions
                        </p>
                        <a class="card-header-icon">
                            <span class="icon is-small" @click="fetchTransactionPage">
                                <i class="fa fa-refresh"></i>
                            </span>
                        </a>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <div v-if="isLoading">
                                <p>
                                    {{ messages.loading }}
                                    <progress class="progress is-small" v-bind:class="{ 'is-danger' : isLoadingFailed }" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
                                    <a v-if="isLoadingFailed" class="button is-link" @click="onRetryFetch">Try Again</a>
                                </p>
                            </div>
                            <div v-else>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><abbr title="Position">ID</abbr></th>
                                            <th>Type</th>
                                            <th>Symbol</th>
                                            <th>Purchase Price</th>
                                            <th>Quantity</th>
                                            <th>Total Amount</th>
                                            <th>Purchase Date</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><abbr title="Position">ID</abbr></th>
                                            <th>Type</th>
                                            <th>Symbol</th>
                                            <th>Purchase Price</th>
                                            <th>Quantity</th>
                                            <th>Total Amount</th>
                                            <th>Purchase Date</th>
                                        </tr>
                                    </tfoot>
                                    <tbody v-if="transactions.length > 0">
                                        <tr v-for="log in transactions" :data-id="log.id">
                                            <td>{{ log.idFormatted }}</td>
                                            <td>{{ log.type }}</td>
                                            <td>{{ log.symbol }}</td>
                                            <td>{{ log.priceFormatted }}</td>
                                            <td>{{ log.qty }}</td>
                                            <td>{{ log.totalFormatted }}</td>
                                            <td><span :title="log.updated_at">{{ log.purchasedTimeAgo }}</span></td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="7" class="has-text-centered"> No records yet </td>
                                        </tr>
                                    </tbody>
                                </table>
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
    import Events from './Events.js';
    export default {
        data: function(){
            return {
                isLoading : true,
                isLoadingFailed : false,
                loadingPercent : 0,
                progressInterval : null,
                transactions : [],
                messages : {
                    loading : "Fetching Latest Transaction"
                },
                api : {
                    getLatestTransactions : hostname + "/api/v1/user/transactions"
                },
                page : 1,
            }
        },
        methods: {
            fetchTransactionPage(){
                var self = this;
                self.isLoadingFailed = false;
                self.isLoading = true;
                self.loadingPercent = 0;
                if(self.progressInterval) clearInterval(self.progressInterval);
                self.progressInterval = setInterval(function(){
                    self.loadingPercent += 1;
                    if(self.loadingPercent>100) self.loadingPercent = 0;
                },50);

                Axios.get(self.api.getLatestTransactions,{ page : self.page }).then(function(response){
                    if(response.status == 200 && response.data.status == 'OK'){
                        self.transactions = response.data.data;
                        self.isLoading = false;
                    }
                    else{
                        self.isLoadingFailed = true;
                    }
                    clearInterval(self.progressInterval);
                }).catch(function(error){
                    self.isLoadingFailed = true;
                });
            },
            onRetryFetch(){
                this.fetchTransactionPage();
            }
        },
        created: function(){
            var self = this;
            this.fetchTransactionPage();

            Events.$on('buyingSuccess',function(symbol){
                self.fetchTransactionPage();
            });
            Events.$on('sellingSuccess',function(symbol){
                self.fetchTransactionPage();
            });
        }
    }
    </script>

    <style lang="css">
    </style>
