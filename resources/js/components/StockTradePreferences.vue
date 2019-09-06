<template>
    <div id="tab-profile">
        <div class="section content">
            <div class="container">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            Profile Settings
                        </p>
                    </header>
                    <div class="card-content">
                        <div v-if="isLoading">
                            <p>
                                {{ messages.loading }}
                                <progress class="progress is-small" v-bind:class="{ 'is-danger' : isLoadingFailed }" v-bind:value="loadingPercent" max="100">{{ loadingPercent }}%</progress>
                                <a v-if="isLoadingFailed" class="button is-link" @click="onRetryFetch">Try Again</a>
                            </p>
                        </div>
                        <div v-else class="media">
                            <div class="media-left">
                                <figure class="image is-128x128" style="height: 128px; width: 128px;">
                                    <photo-uploader
                                    imgDefault="http://bulma.io/images/placeholders/128x128.png" :imgPath='profile.avatar' imgW="128px" imgH="128px" btnClass="button is-small is-fullwidth" postMethod = "/api/v1/user/profile/avatarUpdate"
                                    ></photo-uploader>
                                </figure>
                            </div>
                            <div class="media-content">
                                <form class="control" @submit.prevent="updateProfile">
                                    <div class="columns">
                                        <div class="column is-6">
                                            <label class="label">Fullname</label>
                                            <p class="control">
                                                <input class="input" type="text" name="fullname" placeholder="Your Fullname" v-model="profile.fullname" required>
                                            </p>
                                            <label class="label">Username</label>
                                            <p class="control">
                                                <input class="input" type="text" name="username" placeholder="Your Username" v-model="profile.username" required>
                                            </p>
                                            <label class="label">Email Address</label>
                                            <p class="control">
                                                <input class="input" type="email" name="email" placeholder="Your Email Address" v-model="profile.email">
                                            </p>
                                            <label class="label">Password</label>
                                            <p class="control">
                                                <input class="input" name="password" v-model="profile.password" type="password" placeholder="*********" value="">
                                            </p>
                                            <label class="label">Confirm Password</label>
                                            <p class="control">
                                                <input class="input" name="password_confirmation" v-model="profile.password_confirmation" type="password" placeholder="*********" value="">
                                            </p>
                                            <p>
                                                <div class="block">
                                                    <button type="submit" class="button is-primary" :class="{ 'is-loading' : profileSaving }">Update Profile</button>
                                                </div>
                                            </p>
                                        </div>
                                        <div class="column is-4">
                                            <label class="label">Country</label>
                                            <p class="control">
                                                <span class="select">
                                                    <select v-model="profile.country">
                                                        <option v-for="country in countries">{{ country }}</option>
                                                    </select>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import Axios from 'axios';
import PhotoUploader from './utils/PhotoUploader.vue';

export default {
    data: function(){
        return {
            isLoading : true,
            isLoadingFailed : false,
            loadingPercent : 0,
            progressInterval : null,
            profileSaving : false,
            countries : {},
            messages : {
                loading : "Getting Profile Information..."
            },
            api : {
                getUserProfile : hostname + "/api/v1/user/profile",
                updateUserProfile : hostname + "/api/v1/user/profile/update",
                getCountryList : hostname + "/js/countries.json",
                saveProfilePicture : hostname + "/api/v1/user/profile/photo/update",
            },
            error : { },
            success : { }
        }
    },
    components: {
        'photo-uploader' : PhotoUploader,
    },
    methods : {
        updateProfile(){
            var self = this;
            self.profileSaving = true;

            Axios.post(self.api.updateUserProfile,{ profile : self.profile }).then(function(response){
                if(response.status == 200 && response.data.status == 'OK'){
                    console.log(reponse.data);
                }
                self.profileSaving = false;
            }).catch(function(error){
                self.profileSaving = false;
            });

        },
        fetchProfileData(){
            var self = this;
            self.isLoadingFailed = false;
            self.isLoading = true;
            self.loadingPercent = 0;
            if(self.progressInterval) clearInterval(self.progressInterval);
            self.progressInterval = setInterval(function(){
                self.loadingPercent += 1;
                if(self.loadingPercent>100) self.loadingPercent = 0;
            },50);

            Axios.get(self.api.getUserProfile).then(function(response){
                if(response.status == 200 && response.data.status == 'OK'){
                    self.profile = response.data.profile;
                    self.isLoading = false;
                }
                else{
                    self.isLoadingFailed = true;
                }
                clearInterval(self.progressInterval);
            }).catch(function(error){
                self.isLoadingFailed = true;
            });
        },
        fetchCountries(){
            var self = this;
            Axios.get(self.api.getCountryList).then(function(response){
                if(response.status == 200 && response.data){
                    response.data = Object.keys(response.data).map(function(k) { return response.data[k] });
                    self.countries = response.data.sort();
                }
            });
        }
    },
    created: function(){
        var self = this;
        this.fetchProfileData();
        this.fetchCountries();
    }
}
</script>

<style lang="css">
</style>
