import SideMenu from './components/utils/Menu.vue';
import Sandbox from './components/utils/Sandbox.vue';

const stocktrade = new Vue({
    el: '#docs',
    template : `<div id="docs" class="container">
    <div class="columns">
    <div class="column is-3">
    <sidemenu v-for="section in sections" :sidemenu="section" :defaultMenu="defaultMenu"></sidemenu>
    </div>
    <div class="column">
        <sandbox v-for="section in sections" :box="section" ></sandbox>
    </div>
    </div>
    </div>
    `,
    data: function(){
        return {
            defaultMenu : 'symbol-search',
            sections : {
                stock : {
                    name : 'Stocks',
                    endpoints : [
                        {
                            name : 'Symbol Search',
                            slug : 'symbol-search',
                            url : hostname + 'api/v1/stock/search',
                            method : 'GET',
                            fields : {
                                q : {
                                    type : 'String',
                                    required : true,
                                    default : 'none'
                                }
                            }
                        }
                    ]
                },
                users : {
                    name : 'Players',
                    endpoints : [
                        {
                            name : 'Player Search',
                            slug : 'player-search',
                            url : hostname + 'api/v1/stock/search',
                            method : 'GET',
                            fields : {
                                q : {
                                    type : 'String',
                                    required : true,
                                    default : 'none'
                                }
                            }
                        }
                    ]
                }
            }
        }
    },
    components: {
        'sidemenu' : SideMenu,
        'sandbox' : Sandbox,
    },
    methods: {

    },
    created: function(){


    }
});
