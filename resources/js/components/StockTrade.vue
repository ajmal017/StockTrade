<template>
    <div id="StockTradeWrapper">
        <section class="hero is-primary">
            <div class="hero-head">
                <div class="nav-right nav-menu">
                    <a class="nav-item is-active" @click="onLogout">
                        Logout Account
                    </a>
                </div>
            </div>
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-vcentered">
                        <div class="column">
                            <p class="title">
                                Demo Trading Account
                            </p>
                            <p class="subtitle">
                                Compete, Risk Free with <strong>$100,000</strong> in Virtual Cash
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <tabs tabclass="is-centered is-boxed" :invertColor="true" >
            <tab name="My Portfolio">
                <stocktrade-portfolio></stocktrade-portfolio>
            </tab>
            <tab name="Watchlist">
                <stocktrade-watchlist></stocktrade-watchlist>
            </tab>
            <tab name="Transaction History">
                <stocktrade-transactions></stocktrade-transactions>
            </tab>
            <tab name="Preferences">
                <stocktrade-preferences></stocktrade-preferences>
            </tab>
        </tabs>
    </div>
</template>

<script>
import Axios from 'axios';
import Events from './Events.js';
import StockTradeWatchList from './StockTradeWatchList.vue';
import StockTradePreferences from './StockTradePreferences.vue';
import StockTransactions from './StockTransactions.vue';
import StockPortfolio from './StockPortfolio.vue';
import Tabs from './utils/Tabs.vue';
import Tab from './utils/Tab.vue';


export default {
    data: function(){
        return {
            active : 'portfolio',
            api : {
                logoutAccount : {
                    url : hostname + "/api/v1/user/logout",
                    response : null
                }
            }
        }
    },
    components: {
        'stocktrade-watchlist' : StockTradeWatchList,
        'stocktrade-transactions' : StockTransactions,
        'stocktrade-portfolio' : StockPortfolio,
        'stocktrade-preferences' : StockTradePreferences,
        'tabs' : Tabs,
        'tab' : Tab,
    },
    created: function(){

    },
    methods: {
        onLogout(){
            var self = this;
            Axios.post(self.api.logoutAccount.url).then(function(response){
                if(response.status===200){
                    if(response.data.status=="OK"){
                        Events.$emit('userLoggedOut');
                    }
                }
            });
        },
    }
}
</script>

<style lang="css">
</style>
