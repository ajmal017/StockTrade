<template>
    <div class="stock-buy-wrapper">
        <div class="columns is-multiline">
            <div class="column is-full has-text-centered">
                <h1 class="title is-3">{{ symbol }}</h1>
                <h2 class="subtitle is-6">{{ name }}</h2>
            </div>
            <div class="column is-half">
                <label class="label">Price</label>
                <p class="has-text-right">
                    <input class="input has-text-right" type="number" v-model="price" min="1" readonly>
                </p>
            </div>
            <div class="column is-half">
                <label class="label">Quantity</label>
                <p class="control">
                    <input class="input has-text-right" type="number" v-model="qty" min="1" :max="maxBuy" @change="checkMaxBuy">
                    <span class="help is-success">
                        <a href="#" @click.prevent="qty = ((qty+10) > maxBuy ? maxBuy : qty + 10)">+10</a> |
                        <a href="#" @click.prevent="qty = ((qty+100) > maxBuy ? maxBuy : qty + 100)">+100</a> |
                        <a href="#" @click.prevent="qty = ((qty+500) > maxBuy ? maxBuy : qty + 500)">+500</a> |
                        <a href="#" @click.prevent="qty = ((qty+1000) > maxBuy ? maxBuy : qty + 1000)">+1000</a> |
                        <a href="#" @click.prevent="qty=maxBuy">MAX</a>
                    </span>
                </p>
            </div>
            <div class="column is-half">
                <label class="label">&nbsp;</label>
                <p>
                    <button class="button is-primary is-fullwidth" :class="{ 'is-loading' : isBuying , 'is-disabled' : !canBuy }" @click="onBuyStock" >Buy Now</button>
                </p>
            </div>
            <div class="column is-half">
                <label class="label">Total</label>
                <p class="control">
                    <input class="input has-text-right" type="number" v-model="totalAmount" readonly>
                </p>
            </div>
            <div v-if=" maxBuy < 1 " class="column is-full">
                <blockquote>
                    You have no enough cash balance to buy stocks from {{ symbol }}
                </blockquote>
            </div>
            <div v-if=" isBuyingSuccess!=null " class="column is-full">
                <blockquote v-if=" isBuyingSuccess ">
                    Congratulations! Transaction complete.
                </blockquote>
                <blockquote v-else>
                    <ul>
                        <li v-for="error in buyingErrors">{{ error }}</li>
                    </ul>
                </blockquote>
            </div>
        </div>
    </div>
</template>
<script>
import Axios from 'axios';
import Events from './Events.js';
export default {
    data : function(){
        return {
            maxBuy: 1,
            canBuy: false,
            isBuying : false,
            isBuyingSuccess : null,
            buyingErrors : [],
            qty : 100,
            api : {
                buyStocks : hostname + "/api/v1/stock/buy",
                checkMaxBuy : hostname + "/api/v1/user/getMaxBuy"
            }
        }
    },
    computed: {
        totalAmount(){
            return parseFloat(this.qty * this.price).toFixed(2)
        }
    },
    props: {
        symbol : {
            required : true,
        },
        name : {
            required : true,
        },
        price : {
            required : true,
        }
    },
    methods: {
        checkMaxBuy: function(){
            if(this.qty > this.maxBuy) this.qty = this.maxBuy;
        },
        checkMaxQuantity: function(){
            var self = this;
            self.canBuy = false;

            Axios.get(self.api.checkMaxBuy,{
                params : {
                    symbol : this.symbol,
                }
            }).then(function(response){
                if(response.status == 200 && response.data.status == 'OK'){
                    self.maxBuy = response.data.result.maxBuy;
                    self.canBuy = true;
                    if(self.maxBuy<1) self.canBuy = false;

                    if(self.maxBuy<1){
                         self.canBuy = false;
                         self.qty = 0;
                    }

                    if(self.qty>self.maxBuy){
                         self.qty = self.maxBuy;
                    }
                }
            }).catch(function(error){
                self.canBuy = false;
            });
        },
        onBuyStock: function(){
            var self = this;
            self.isBuying = true;
            self.isBuyingSuccess = null;
            Axios.post(self.api.buyStocks,{
                symbol : this.symbol,
                price : this.price,
                qty : this.qty
            }).then(function(response){
                self.isBuying = false;
                if(response.status == 200 && response.data.status == 'OK'){
                    Events.$emit('buyingSuccess',self.symbol);
                    self.isBuyingSuccess = true;
                }
            }).catch(function(error){
                self.isBuying = false;
                self.isBuyingSuccess = false;
                self.buyingErrors = error.response.data.error;
            });
        }
    },
    created: function(){
        var self = this;
        self.checkMaxQuantity();
    }
}
</script>
<style lang="css">
</style>
