<template>
    <div class="tabs-bulma" :class="{ 'is-primary' : invertColor }">
        <div class="tabs" :class="tabclass">
            <ul>
                <li v-for="tab in tabs" :class="{ 'is-active' : tab.isActive }">
                    <a href="#" @click.prevent="setIsActive(tab)">
                        <span v-if="icon" class="icon is-small"><i class="fa" :class="icon"></i></span>
                        <span>{{ tab.name }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-panes">
            <slot></slot>
        </div>
    </div>
</template>

<script>

import Events from '../Events.js';

export default {
    name: 'Tabs',
    data: function(){
        return {
            tabs : [],
        }
    },
    props: {
        invertColor : {
            default : false
        },
        tabclass : {
            type: String,
            default : 'is-centered'
        },
        icon : {
            default : false
        }
    },
    created: function(){
        this.tabs = this.$children;
    },
    methods: {

        setIsActive(clickedTab){
            Events.$emit('tabClicked',clickedTab);
            this.tabs.forEach(tab => {
                tab.isActive = tab.name==clickedTab.name;
            })

            this.$localStorage.set('activeTabStocktrade',clickedTab.name);

        }
    }
}
</script>

<style lang="css">
</style>
