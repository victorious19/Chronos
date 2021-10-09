<template>
  <div class="modal">
    <fieldset>
      <legend>Event details</legend>
      <p><b>Title:</b>&nbsp;<input type="text" v-model="event.title"></p>
      <p><b>Description:</b>&nbsp;<textarea v-model="event.description" /></p>
      <p><b>Start:</b>&nbsp;<input type="datetime-local" v-model="event.start"></p>
      <p><b>End:</b>&nbsp;<input type="datetime-local" v-model="event.end"></p>
      <p><b>Color:</b>&nbsp;<input type="color" v-model="event.color"></p>
      <div class="form_radio_group">
        <div class="form_radio_group-item">
          <input id="radio-2" type="radio" name="radio" value="arrangement" v-model="event.category">
          <label for="radio-2">Arrangement</label>
        </div>
        <div class="form_radio_group-item">
          <input id="radio-1" type="radio" name="radio" value="reminder" v-model="event.category">
          <label for="radio-1">Reminder</label>
        </div>
        <div class="form_radio_group-item">
          <input id="radio-3" type="radio" name="radio" value="task" v-model="event.category">
          <label for="radio-3">Task</label>
        </div>
      </div><br />
      <div style="display: flex">
        <button class="btn outline danger-outline" @click="deleteEvent">Delete</button>
        <button @click="updateEvent" class="btn outline primary-outline">Update</button>
      </div>
    </fieldset>
  </div>
</template>

<script>
import axios from "axios";
import Cookies from "js-cookie";

export default {
  data() {
    return {
      event: {},
    }
  },
  mounted() {
    axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
    axios.get('http://127.0.0.1:8000/api/events/'+this.event_id)
        .then(res => {
          this.event = res.data
          this.event.start = this.format_date(this.event.start)
          this.event.end = this.format_date(this.event.end)
        })
        .catch(err => {
          this.$emit('close')
          alert(err)
        })
  },
  methods: {
    deleteEvent(event) {
      event.preventDefault()
      axios.delete('http://127.0.0.1:8000/api/events/'+this.event_id)
          .then(() => {
            this.getEvents()
            this.$emit('close')
          })
          .catch(err => {
            alert(err)
            this.$emit('close')
          })
    },
    updateEvent(event) {
      event.preventDefault()
      axios.patch('http://127.0.0.1:8000/api/events/'+this.event_id, {
        'start': this.event.start,
        'end': this.event.end,
        'title': this.event.title,
        'color': this.event.color,
        'description': this.event.description,
        'category': this.event.category
      })
          .then(() => {
            this.getEvents()
            this.$emit('close')
          })
          .catch(err => {
            alert(err)
            this.$emit('close')
          })
    },
  },
  props: {
    event_id: String,
    getEvents: Function,
    format_date: Function
  },
};
</script>

<style>
.modal {
  margin: 20px 0px 20px 20px;
  overflow:scroll;
  height: 100%;
}
.form_radio_group {
  display: inline-block;
  overflow: hidden;
}
.form_radio_group-item {
  display: inline-block;
  float: left;
}
.form_radio_group input[type=radio] {
  display: none;
}
.form_radio_group label {
  display: inline-block;
  cursor: pointer;
  padding: 0px 15px;
  line-height: 34px;
  border: 1px solid #999;
  user-select: none;
}
.form_radio_group input[type=radio]:checked + label {
  background: #a6b5ff;
}
.modal fieldset p {
  white-space: pre;
}
.btn {
  box-sizing: border-box;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-color: transparent;
  border-radius: 0.6em;
  cursor: pointer;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-align-self: center;
  align-self: center;
  font-size: 1rem;
  line-height: 1;
  margin: 10px;
  padding: 10px 20px;
  text-decoration: none;
  text-align: center;
  text-transform: uppercase;
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
}
.btn:hover, .btn:focus {
  color: #fff;
  outline: 0;
}

.outline {
  -webkit-transition: box-shadow 300ms ease-in-out, color 300ms ease-in-out;
  transition: box-shadow 300ms ease-in-out, color 300ms ease-in-out;
}
.danger-outline:hover {
  box-shadow: 0 0 40px 40px #e74c3c inset;
}
.primary-outline:hover {
  box-shadow: 0 0 40px 40px #3c61e7 inset;
}
</style>
