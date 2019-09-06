<template>
    <div class="photo-uploader">
        <template v-if="!image">
            <img :src="imgDefault" :width="imgW" :height="imgH" @click.self="triggerFileBrowser"/>
            <button @click.prevent="removeImage" :class="btnClass">Upload Profile</button>
        </template>
        <template v-else>
            <img :src="image" :width="imgW" :height="imgH" @click.self="triggerFileBrowser"/>
            <button @click.prevent="removeImage" :class="btnClass">Change Profile</button>
        </template>
        <input type="file" @change="onFileChange" class="hidden" ref="btnBrowse" />
    </div>
</template>
<script>
import Axios from 'axios';
export default {
    data: function(){
        return {
            image : '',
            imageLoading : '/img/loading.gif'
        }
    },
    props : {
        imgPath : {
            type : String,
        },
        btnClass : {
            type : String,
            default : ''
        },
        imgDefault : {
            type : String,
            default : 'http://bulma.io/images/placeholders/128x128.png'
        },
        imgW : {
            type : String,
            default : '100%'
        },
        imgH : {
            type : String,
            default : '100%'
        },
        postMethod : {
            type : String,
            required : true
        }
    },
    methods: {
        triggerFileBrowser(){
            if(!this.imgPath || this.imgPath === this.placeholder) this.$refs.btnBrowse.click()
        },
        onFileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
            return;
            this.createImage(files[0]);
            this.uploadFile(files[0]);
        },
        createImage(file) {
            this.image = this.imageLoading;
        },
        removeImage: function (e) {
            this.$refs.btnBrowse.click();
        },
        uploadFile(file){
            var self = this;
            var data = new FormData();
            data.append('photo', file);
            Axios.post(self.postMethod, data, {
                onUploadProgress: function(progressEvent) {
                    var percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                }
            })
            .then(function (response) {
                self.image = response.data.profile;
            })
            .catch(function (err) {
                self.image = self.placeholder;
            });
        }
    },
    created : function(){
        if(this.imgPath) this.image = this.imgPath;
        else if(this.imgDefault) this.image = this.default;
        else this.image = this.imageLoading;
    }
}
</script>

<style lang="scss">
.photo-uploader{
    height: inherit;
    width: inherit;
    button{
        margin-top: 1rem;
    }
}
</style>
