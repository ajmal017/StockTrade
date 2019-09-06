<template>
    <div class="stock-sell-wrapper">
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
                    <input class="input has-text-right" type="number" v-model="qty" min="1" :max="maxSell" @change="checkmaxSell">
                    <span class="help is-success">
                        <a href="#" @click.prevent="qty = ((qty+10) > maxSell ? maxSell : qty + 10)">+10</a> |
                        <a href="#" @click.prevent="qty = ((qty+100) > maxSell ? maxSell : qty + 100)">+100</a> |
                        <a href="#" @click.prevent="qty = ((qty+500) > maxSell ? maxSell : qty + 500)">+500</a> |
                        <a href="#" @click.prevent="qty = ((qty+1000) > maxSell ? maxSell : qty + 1000)">+1000</a> |
                        <a href="#" @click.prevent="qty=maxSell">MAX</a>
                    </span>
                </p>
            </div>
            <div class="column is-half">
                <label class="label">&nbsp;</label>
                <p>
                    <button class="button is-primary is-fullwidth" :class="{ 'is-loading' : isSelling , 'is-disabled' : !canSell }" @click="onSellStock" >Sell Now</button>
                </p>
            </div>
            <div class="column is-half">
                <label class="label">Total</label>
                <p class="control">
                    <input class="input has-text-right" type="number" v-model="totalAmount" readonly>
                </p>
            </div>
            <div v-if=" maxSell < 1 " class="column is-full">
                <blockquote>
                    You have no outstanding stocks for {{ symbol }}
                </blockquote>
            </div>
            <div v-if=" isSellingSuccess!=null " class="column is-full">
                <blockquote v-if=" isSellingSuccess ">
                    Congratulations! Transaction complete.
                </blockquote>
                <blockquote v-else>
                    <ul>
                        <li v-for="error in sellingErrors">{{ error }}</li>
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
            maxSell: 1,
            canSell: false,
            isSelling : false,
            isSellingSuccess : null,
            sellingErrors : [],
            qty : 100,
            api : {
                sellStocks : hostname + "/api/v1/stock/sell",
                checkMaxSell : hostname + "/api/v1/user/getMaxSell"
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
        checkmaxSell: function(){
            if(this.qty > this.maxSell) this.qty = this.maxSell;
        },
        checkMaxQuantity: function(){
            var self = this;
            self.canSell = false;

            Axios.get(self.api.checkMaxSell,{
                params : {
                    symbol : this.symbol,
                }
            }).then(function(response){
                if(response.status == 200 && response.data.status == 'OK'){

                    self.maxSell = response.data.result.maxSell;
                    self.canSell = true;

                    if(self.maxSell<1){
                         self.canSell = false;
                         self.qty = 0;
                    }

                    if(self.qty>self.maxSell){
                        self.qty = self.maxSell;
                    }
                }
            }).catch(function(error){
                self.canSell = false;
            });
        },
        onSellStock: function(){
            var self = this;
            self.isSelling = true;
            self.isSellingSuccess = null;
            Axios.post(self.api.sellStocks,{
                symbol : this.symbol,
                price : this.price,
                qty : this.qty
            }).then(function(response){
                self.isSelling = false;
                if(response.status == 200 && response.data.status == 'OK'){
                    Events.$emit('sellingSuccess',self.symbol);
                    self.isSellingSuccess = true;
                }
            }).catch(function(error){
                self.isSelling = false;
                self.isSellingSuccess = false;
                self.sellingErrors = error.response.data.error;
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
