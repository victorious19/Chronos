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
        <button @click="create" class="btn outline primary-outline">Save</button>
      </div>
    </fieldset>
  </div>
</template>

<script>
import axios from "axios";
import Cookies from "js-cookie";

export default {
  props: {
    event: Object,
    getEvents: Function,
    format_date: Function
  },
  mounted() {
    if(this.event) {
      this.event.start = this.format_date(this.event.start)
      this.event.end = this.format_date(this.event.end)
      this.event.color = '#0000ff'
      this.event.category = 'reminder'
    }
  },
  methods: {
    create(event) {
      event.preventDefault()
      axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
      axios.post('http://127.0.0.1:8000/api/events', {
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
    }
  }

}
</script>

<style>

</style>
