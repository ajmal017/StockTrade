<template>
    <div class="tab-pane" v-show="isActive">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name : 'tab',
    data : function(){
        return {
            isActive : false
        }
    },
    props: {
        name : {
            required: true,
        },
        selected : {
            type: Boolean,
            default: false,
        },
    },
    created: function(){
        this.isActive = this.selected;

        if (typeof(Storage) !== "undefined") {

            var activeTabStocktrade = this.$localStorage.get('activeTabStocktrade')
            if(activeTabStocktrade){
                this.isActive = activeTabStocktrade == this.name;
            }
        }
    }
}
</script>

<style lang="css">
</style>
