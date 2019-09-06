<template>
    <form id="stocktrade-signup" class="is-boxed" @submit.prevent="onSubmitForm">
        <div class="form-group" v-for="input in inputs">
            <label class="label">{{ input.label }}</label>
            <p class="control has-icon has-icon-right">

                <input v-if="input.type == 'text'" class="input" :class="{ 'is-success' : input.valid===true , 'is-danger' : input.valid===false }" type="text" :name="input.name" :required="input.required" v-model="input.value" :placeholder="input.placeholder" @change="validateForm">
                <input v-if="input.type == 'password'" class="input" :class="{ 'is-success' : input.valid===true , 'is-danger' : input.valid===false }" type="password" :name="input.name" :required="input.required" v-model="input.value" :placeholder="input.placeholder" @change="validateForm">


                <span v-if="input.valid!==null" class="icon is-small">
                    <i v-if="input.valid" class="fa fa-check"></i>
                    <i v-else class="fa fa-warning"></i>
                </span>
                <span class="help" v-if="input.message" :class="{ 'is-success' : input.valid===true , 'is-danger' : input.valid===false }">{{ input.message[input.valid] }}</span>
            </p>
        </div>
        <hr>
        <p class="control">
            <button class="button is-primary" :class="{ 'is-disabled' : isFormValid==false , 'is-loading' : isFormSubmitting }" type="submit">Login</button>
            <button class="button is-default" type="reset" @click="onResetForm">Cancel</button>
        </p>

        <div v-if="userLoginStatus==false" class="notification is-danger">
            <button class="delete" @click="userLoginStatus = null"></button>
            <ul>
                <li v-for="errors in userLoginErrors">{{ errors }}</li>
            </ul>
        </div>
    </form>
</template>

<script>
import Axios from 'axios';
import Events from './Events.js';

export default {
    data : function(){
        return {
            isFormValid : null,
            isFormSubmitting : false,
            userLoginStatus : null,
            userLoginErrors : [],
            inputs : {
                username : {
                    label : 'Username',
                    name : 'username',
                    type : 'text',
                    valid : null,
                    value : '',
                    required : true,
                },
                password : {
                    label : 'Password',
                    name : 'password',
                    type : 'password',
                    valid : null,
                    value : '',
                    required : true,
                }
            },
            api : {
                isRegistered : hostname + "/api/v1/user/authenticate"
            },
        }
    },
    methods : {
        onResetForm(event){
            this.isFormValid = false;
            for(var i in this.inputs){
                this.inputs[i].value = '';
                this.inputs[i].valid = null;
            }
        },
        onSubmitForm(event){
            var self = this;
            self.isFormSubmitting = true;
            Axios.post(self.api.isRegistered,{
                username: self.inputs['username'].value,
                password: self.inputs['password'].value
            }).then(function(response){
                self.isFormSubmitting = false;
                if(response.status===200){
                    if(response.data.status=="OK"){
                         self.userLoginStatus = true;
                         Events.$emit('userLoggedIn',response.data.user);
                    }
                    else if(response.data.status=="FAILED"){
                         self.userLoginStatus = false;
                         self.userLoginErrors = response.data.error;
                    }
                    else self.inputs['username'].valid = null;
                }else{
                    self.userLoginStatus = false;
                }
            });
        },
        validateForm(){
            this.isFormValid = true;
            this.userLoginStatus = null;
            // Valid Username

            for(var i in this.inputs){

                // Check if this input is required
                if(this.inputs[i].required && this.inputs[i].value==''){
                    this.inputs[i].valid = null;
                    this.isFormValid = false;
                    break;
                }

                // Check if this input is invalid, if yes make the whole form invalid
                if(this.inputs[i].valid==false){
                    this.isFormValid = false;
                    break;
                }
            }
            return this.isFormValid;
        }
    },
    created: function(){
        this.validateForm()
    }
}
</script>

<style lang="css">
</style>
