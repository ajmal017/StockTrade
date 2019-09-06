<template>
    <form id="stocktrade-registration" class="is-boxed" @submit.prevent="onSubmitForm">
        <div class="form-group" v-for="input in inputs">
            <label class="label">{{ input.label }}</label>
            <p class="control has-icon has-icon-right">

                <input v-if="input.type == 'text'" class="input" :class="{ 'is-success' : input.valid===true , 'is-danger' : input.valid===false }" type="text" :name="input.name" :required="input.required" v-model="input.value" :placeholder="input.placeholder" @change="validateForm" @keyup="input.onKeyup">
                <input v-if="input.type == 'password'" class="input" :class="{ 'is-success' : input.valid===true , 'is-danger' : input.valid===false }" type="password" :name="input.name" :required="input.required" v-model="input.value" :placeholder="input.placeholder" @change="validateForm" @keyup="input.onKeyup">


                <span v-if="input.valid!==null" class="icon is-small">
                    <i v-if="input.valid" class="fa fa-check"></i>
                    <i v-else class="fa fa-warning"></i>
                </span>
                <span class="help" v-if="input.message" :class="{ 'is-success' : input.valid===true , 'is-danger' : input.valid===false }">{{ input.message[input.valid] }}</span>
            </p>
        </div>

        <hr>
        <p class="control">
            <button class="button is-primary" :class="{ 'is-disabled' : isFormValid==false , 'is-loading' : isFormSubmitting }" type="submit">Create Account</button>
            <button class="button is-default" type="reset" @click="onResetForm">Reset</button>
        </p>

        <div v-if="userRegisterStatus==true" class="notification is-primary">
            <button class="delete" @click="userRegisterStatus = null"></button>
            Your account has been created successfully. Start trading now!
        </div>

        <div v-if="userRegisterStatus==false" class="notification is-danger">
            <button class="delete" @click="userRegisterStatus = null"></button>
            Something went wrong. Registration Failed.<br>
            <ul>
                <li v-for="errors in userRegisterErrors">{{ errors }}</li>
            </ul>
        </div>

    </form>
</template>

<script>
import Axios from 'axios';

export default {
    data : function(){
        return {
            isFormValid : null,
            isFormSubmitting : false,
            userRegisterStatus : null,
            userRegisterErrors: [],
            usernameCheckInterval : null,
            usernameCheckTreshold : 1000,
            api : {
                checkUsername : hostname + "/api/v1/user/isUsernameAvailable",
                createAccount : hostname + "/api/v1/user/create"
            },
            inputs : {
                username : {
                    label : 'Username',
                    name : 'username',
                    type : 'text',
                    valid : null,
                    value : '',
                    required : true,
                    message : {
                        'true' : 'This username is available',
                        'false' : 'This username already taken',
                        'null' : '6-12 Characters',
                    },
                    onKeyup : this.checkUsername
                },
                fullname : {
                    label : 'Fullname',
                    name : 'fullname',
                    type : 'text',
                    valid : null,
                    value : '',
                    required : true,
                    onKeyup : function(){

                    }
                },
                password : {
                    label : 'Password',
                    name : 'password',
                    type : 'password',
                    valid : null,
                    value : '',
                    required : true,
                    onKeyup : function(){

                    }
                }
            }
        };
    },
    methods: {
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
            Axios.post(self.api.createAccount,{
                username: self.inputs['username'].value,
                fullname: self.inputs['fullname'].value,
                password: self.inputs['password'].value
            }).then(function(response){
                self.isFormSubmitting = false;
                if(response.status===200){
                    if(response.data.status=="OK"){
                         self.userRegisterStatus = true;
                         self.onResetForm();
                    }
                    else if(response.data.status=="FAILED"){
                         self.userRegisterStatus = false;
                         self.userRegisterErrors = response.data.error;
                    }
                    else self.inputs['username'].valid = null;
                }else{
                    self.userRegisterStatus = false;
                }
            });
        },
        checkUsername(event){
            var self = this;
            self.isFormValid = false;
            self.inputs['username'].valid = null;
            if(self.inputs['username'].value==''){
                self.inputs['username'].valid = null;
                return;
            }

            if(this.usernameCheckInterval){
                clearInterval(this.usernameCheckInterval);
            }

            this.usernameCheckInterval = setTimeout(function(){
                Axios.post(self.api.checkUsername,{
                    username: self.inputs['username'].value
                }).then(function(response){
                    if(response.status===200){
                        if(response.data.status=="available") self.inputs['username'].valid = true;
                        else if(response.data.status=="taken") self.inputs['username'].valid = false;
                        else self.inputs['username'].valid = null;

                        self.validateForm();
                    }
                });
            },this.usernameCheckTreshold);
        },
        validateForm(){
            this.isFormValid = true;
            this.userRegisterStatus = null;
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
