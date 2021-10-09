<template>
  <div class="container">
    <h1>Create Calendar</h1>
    <div>
    <input type="text" v-model="title">
    <button @click.prevent="create_calendar">Create</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Cookies from "js-cookie";

export default {
  name: "AddCalendarModal",
  data() {
    return {
      title: ''
    }
  },
  props: {
    get_calendars:Function
  },
  methods: {
    create_calendar() {
      axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
      axios.post('http://127.0.0.1:8000/api/calendars', {title: this.title})
          .then(() => {
            this.get_calendars()
            this.$emit('close')
          })
          .catch(err => {
            this.$emit('close')
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
.container input, .container button {
  padding: 10px 20px;
  font-size: 10pt;
  border: 1px solid #cccccc;
  border-radius: 3px;
}
input[type="text"] {
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