<template>
    <div id="stocktrade-login">
        <section class="hero is-primary">
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
        <div class="container">
            <div class="columns">
                <div class="column">
                    <div class="section content">
                        <h3>Create your demo account</h3>
                        <stocktrade-registration></stocktrade-registration>
                    </div>
                </div>
                <div class="column">
                    <div class="section content">
                        <h3>Login to start trading</h3>
                        <stocktrade-signin></stocktrade-signin>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import StockTradeRegistration from './StockTradeRegistration.vue';
import StockTradeSignin from './StockTradeSignin.vue';

export default {
    name : 'stocktrade-login',
    components : {
        'stocktrade-registration' : StockTradeRegistration,
        'stocktrade-signin' : StockTradeSignin,
    }
}
</script>

<style lang="css">
</style>
