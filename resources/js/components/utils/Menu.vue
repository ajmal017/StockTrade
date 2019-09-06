<template>
    <aside class="menu">
        <p class="menu-label">
            {{ sidemenu.name }}
        </p>
        <ul class="menu-list">
            <li v-for="e in sidemenu.endpoints">
                <a :href="( '#'+e.slug )" @click.prevent="setActive(e.slug)" :class="{ 'is-active' : e.slug === activeMenu }" >
                    {{ e.name }}
                </a>
            </li>
        </ul>
    </aside>
</template>

<script>
import Events from '../Events.js';
export default {
    data: function(){
        return {
            activeMenu : null
        }
    },
    props : {
        defaultMenu : '',
        sidemenu : {
            type : Object,
            required : true,
            default : function(){
                return {}
            }
        }
    },
    methods: {
        setActive(slug){
            this.activeMenu = slug;
            Events.$emit('menuClicked',slug);
        },
        animate(elem, style, unit, from, to, time, prop) {
            if (!elem) {
                return;
            }
            var start = new Date().getTime(),
            timer = setInterval(function () {
                var step = Math.min(1, (new Date().getTime() - start) / time);
                if (prop) {
                    elem[style] = (from + step * (to - from))+unit;
                } else {
                    elem.style[style] = (from + step * (to - from))+unit;
                }
                if (step === 1) {
                    clearInterval(timer);
                }
            }, 25);
            if (prop) {
                elem[style] = from+unit;
            } else {
                elem.style[style] = from+unit;
            }
        }

    },
    created: function() {

        var self = this;

        Events.$on('menuClicked',function(slug){
            self.activeMenu = slug;
            var target = document.getElementById(slug);
            if(!target) return;
            self.animate(document.scrollingElement || document.documentElement, "scrollTop", "", 0, target.offsetTop, 2000, true);
            window.location.hash = '#' + slug;
        });

        if(self.defaultMenu!=''){
            self.activeMenu = self.defaultMenu;
            Events.$emit('menuClicked',self.activeMenu);
        }
    }
}
</script>

<style lang="css">
</style>
