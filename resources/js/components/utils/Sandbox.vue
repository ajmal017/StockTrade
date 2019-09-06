<template >
    <div class="sandbox">
        <h1 class="title"></h1>
        <tabs tabclass="is-right">
            <tab :name="b.name" :selected="true" v-for="b in box.endpoints" :key="b.id">
                {{ b.name }}
            </tab>
        </tabs>
    </div>
</template>

<script>
import Events from '../Events.js';
import Tabs from './Tabs.vue';
import Tab from './Tab.vue';
export default {
    data: function(){
        return {

        }
    },
    props : {
        box : {
            type : Object,
            required : true,
            default : function(){
                return {}
            }
        }
    },
    components : {
        'tabs' : Tabs,
        'tab' : Tab,
    },
    methods: {
    },
    created: function() {

    }
}
</script>

<style lang="css">
</style>
