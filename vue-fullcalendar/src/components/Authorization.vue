<template>
  <div class="login-wrap">
    <div class="login-html">
      <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Sign In</label>
      <input id="tab-2" type="radio" name="tab" class="sign-up"><label for="tab-2" class="tab">Sign Up</label>
      <div class="login-form">
        <form class="sign-in-htm" @submit="login">
          <div class="group">
            <label for="user0" class="label">Username</label>
            <input id="user0" type="text" class="input" v-model="username">
            <div class="invalid-feedback" v-if="errors.login">{{errors.login[0]}}</div>
          </div>
          <div class="group">
            <label for="pass0" class="label">Password</label>
            <input id="pass0" type="password" class="input" data-type="password" v-model="password">
            <div class="invalid-feedback" v-if="errors.password">{{errors.password[0]}}</div>
            <div class="invalid-feedback" >{{errors.main}}</div>
          </div>
          <div class="group">
            <input type="submit" class="button" value="Sign In">
          </div>
          <div class="hr"></div>
          <div class="foot-lnk">
            <a href="/forgot-password">Forgot Password?</a>
          </div>
        </form>
        <form class="sign-up-htm" @submit="register">
          <div class="group">
            <label for="user" class="label">Username</label>
            <input id="user" type="text" class="input" v-model="username">
            <div class="invalid-feedback" v-if="errors.login">{{errors.login[0]}}</div>
          </div>
          <div class="group">
            <label for="pass" class="label">Password</label>
            <input id="pass" type="password" class="input" data-type="password" v-model="password">
            <div class="invalid-feedback" v-if="errors.password">{{errors.password[0]}}</div>
          </div>
          <div class="group">
            <label for="pass1" class="label">Repeat Password</label>
            <input id="pass1" type="password" class="input" data-type="password" v-model="password_confirmation">
          </div>
          <div class="group">
            <label for="email" class="label">Email Address</label>
            <input id="email" type="text" class="input" v-model="email">
            <div class="invalid-feedback" v-if="errors.email">{{errors.email[0]}}</div>
            <div class="invalid-feedback" >{{errors.main}}</div>
          </div>
          <div class="group">
            <input type="submit" class="button" value="Sign Up">
          </div>
          <div class="hr"></div>
          <div class="foot-lnk">
            <label for="tab-1">Already Member?</label>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Cookies from 'js-cookie'
export default {
  name: "Authorization",
  data() {
    return {
      errors: {main:''},
      username: '',
      email: '',
      password: '',
      password_confirmation: '',
    }
  },
  methods: {
    async login(event) {
      event.preventDefault()
      this.errors = {main: ''}

      axios.post('http://127.0.0.1:8000/api/auth/login', {
        'login': this.username,
        'password': this.password
      })
      .then(res => {
        Cookies.set('token', res.data.token)
        this.$router.push({path: '/home'})
      })
      .catch(err => {
        if (err.response.data.errors) this.errors = err.response.data.errors
        else if(err.response.data.message) this.errors.main =  err.response.data.message
        else this.errors.main =  err.response.statusText
      })
    },
    async register(event) {
      event.preventDefault()
      this.errors = {main: ''}

      axios.post('http://127.0.0.1:8000/api/auth/register', {
        'login':this.username,
        'password':this.password,
        'password_confirmation':this.password_confirmation,
        'email':this.email
      })
          .then(res => {
            Cookies.set('token', res.data.token)
            this.$router.push({path: '/home'})
          })
          .catch(err => {
            if (err.response.data.errors) this.errors = err.response.data.errors
            else if(err.response.data.message) this.errors.main =  err.response.data.message
            else this.errors.main =  err.response.statusText
          })
    },
  }
}
</script>
<style>
.invalid-feedback {
  color: #a30000;
  font-size: 11pt;
  word-wrap: break-word;
  margin-top: 2px;
}
</style>
<style scoped>
*,:after,:before{box-sizing:border-box}
.clearfix:after,.clearfix:before{content:'';display:table}
.clearfix:after{clear:both;display:block}
a{color:inherit;text-decoration:none}

.login-wrap{
  color:#6a6f8c;
  background: #87b3e8;
  font:600 16px/18px 'Open Sans',sans-serif;
  width:100%;
  margin:auto;
  max-width:525px;
  min-height:670px;
  position:relative;
  box-shadow:0 12px 15px 0 rgba(0,0,0,.24),0 17px 50px 0 rgba(0,0,0,.19);
}
.login-html{
  width:100%;
  height:100%;
  position:absolute;
  padding:90px 70px 50px 70px;
  background:rgba(40,57,101,.9);
}
.login-html .sign-in-htm,
.login-html .sign-up-htm{
  top:0;
  left:0;
  right:0;
  bottom:0;
  position:absolute;
  transform:rotateY(180deg);
  backface-visibility:hidden;
  transition:all .4s linear;
}
.login-html .sign-in,
.login-html .sign-up,
.login-form .group .check{
  display:none;
}
.login-html .tab,
.login-form .group .label,
.login-form .group .button{
  text-transform:uppercase;
}
.login-html .tab{
  font-size:22px;
  margin-right:15px;
  padding-bottom:5px;
  margin:0 15px 10px 0;
  display:inline-block;
  border-bottom:2px solid transparent;
}
.login-html .sign-in:checked + .tab,
.login-html .sign-up:checked + .tab{
  color:#fff;
  border-color:#1161ee;
}
.login-form{
  min-height:345px;
  position:relative;
  perspective:1000px;
  transform-style:preserve-3d;
}
.login-form .group{
  margin-bottom:15px;
}
.login-form .group .label,
.login-form .group .input,
.login-form .group .button{
  width:100%;
  color:#fff;
  display:block;
}
.login-form .group .input,
.login-form .group .button{
  border:none;
  padding:15px 20px;
  border-radius:25px;
  background:rgba(255,255,255,.1);
}
.login-form .group input[data-type="password"]{
  text-security:circle;
  -webkit-text-security:circle;
}
.login-form .group .label{
  color:#aaa;
  font-size:12px;
}
.login-form .group .button{
  background:#1161ee;
}
.login-form .group label .icon{
  width:15px;
  height:15px;
  border-radius:2px;
  position:relative;
  display:inline-block;
  background:rgba(255,255,255,.1);
}
.login-form .group label .icon:before,
.login-form .group label .icon:after{
  content:'';
  width:10px;
  height:2px;
  background:#fff;
  position:absolute;
  transition:all .2s ease-in-out 0s;
}
.login-form .group label .icon:before{
  left:3px;
  width:5px;
  bottom:6px;
  transform:scale(0) rotate(0);
}
.login-form .group label .icon:after{
  top:6px;
  right:0;
  transform:scale(0) rotate(0);
}
.login-form .group .check:checked + label{
  color:#fff;
}
.login-form .group .check:checked + label .icon{
  background:#1161ee;
}
.login-form .group .check:checked + label .icon:before{
  transform:scale(1) rotate(45deg);
}
.login-form .group .check:checked + label .icon:after{
  transform:scale(1) rotate(-45deg);
}
.login-html .sign-in:checked + .tab + .sign-up + .tab + .login-form .sign-in-htm{
  transform:rotate(0);
}
.login-html .sign-up:checked + .tab + .login-form .sign-up-htm{
  transform:rotate(0);
}

.hr{
  height:2px;
  margin:30px 0 20px 0;
  background:rgba(255,255,255,.2);
}
.foot-lnk{
  text-align:center;
}
</style>