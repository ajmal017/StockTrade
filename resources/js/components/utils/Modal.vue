<template>
    <div class="modal-wrapper">
        <a @click=" isModalOpen = true " :class="btnClass ? btnClass : 'button is-small'" v-html="btnText"></a>
        <div class="modal is-active" v-if="isModalOpen">
            <div class="modal-background" @click=" isModalOpen = false "></div>
            <div class="modal-card">
                <header v-if="!minimal" class="modal-card-head">
                    <p class="modal-card-title is-marginless"><slot name="header"></slot></p>
                    <button class="delete" @click=" isModalOpen = false "></button>
                </header>
                <section class="modal-card-body" :class="{ 'has-minheight' : minheight }">
                    <slot></slot>
                </section>
                <footer v-if="!minimal" class="modal-card-foot">
                    <slot name="footer">
                        <a class="button is-pulled-right" @click=" isModalOpen = false ">Close</a>
                    </slot>
                </footer>
            </div>
        </div>
    </div>
</template>


<script>
export default{
    name : 'modal',
    props : {
        btnText : {
            type : String,
            required : true
        },
        btnClass : {
            type : String,
            required : true
        },
        minimal : {
            type : Boolean,
            default : false,
        },
        minheight : {
            type : Boolean,
            default : true,
        },
    },
    data : function(){
        return {
            isModalOpen : false
        }
    },
    isComputed : {
        btnClass : 'button is-small'
    }
}
</script>
