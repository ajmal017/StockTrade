
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 var apihost = 'http://localhost:8000';
 var stocktradeWatchList = new Vue({
     el: '#stocktrade-watchlist',
     data: {
         isLoading: true,
         message: 'Hello Vue!',
         messages : {
             loading : 'Getting favorite quotes...'
         },
         api : {
             getWatchlist : {
                 url : apihost + "/user/watchlist",
                 response : null
             }
         }
     },
     components: {
         'stockitem' : {
             props: ['symbol'],
             template : '<li>{{ symbol }}</li>',
             data : function(){
                 return {
                     symbol : ''
                 };
             }
         }
     },
     methods: {
         fetchData: function () {
             var xhr = new XMLHttpRequest()
             var self = this
             xhr.open('GET', self.api.getWatchlist.url)
             xhr.onreadystatechange = function (oEvent) {
                 if (xhr.readyState === 4) {
                     if (xhr.status === 200) {
                         self.api.getWatchlist.response = JSON.parse(xhr.responseText)
                         self.isLoading = false;

                     } else {
                         self.messages.loading = 'Error: Failed getting watchlist';
                     }
                 }
             };
             xhr.send()
         }
     },
     created: function(){
         console.log('component ready');

         this.fetchData();

     }
 })