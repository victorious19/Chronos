<template>
  <div class="container">
    <h1>Invite somebody</h1>
    <div>
      <select v-model="selected">
        <option v-for="user in users" :value="user.id" :key="user.id">{{user.login}}</option>
      </select>
      <button @click="invite">Apply</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Cookies from "js-cookie";

export default {
  name: "InviteModal",
  data() {
    return {
      users: [],
      selected: ''
    }
  },
  props: {
    calendar_id: String
  },
  mounted() {
    axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
    axios.get('http://127.0.0.1:8000/api/users')
        .then(res => {
          this.users = res.data
          this.selected = res.data[0].id
        })
        .catch(err => {
          alert(err)
        })
  },
  methods: {
    invite() {
      axios.post('http://127.0.0.1:8000/api/calendars/'+this.calendar_id+'/invites', {user_id: this.selected})
          .then(() => {
            this.$emit('close')
          })
          .catch(err => {
            alert(err)
          })
    }
  }
}
</script>

<style scoped>
.container {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.container div * {
  padding: 10px 20px;
  font-size: 10pt;
  border: 1px solid #cccccc;
  border-radius: 3px;
}
select {
  background: #ffffff !important;
  outline: none;
  margin-right: 5px;
}
button {
  background-color: white;
}
button:hover {
  background-color: #222297;
  color: white;
  transition: all 0.5s;
}
</style>