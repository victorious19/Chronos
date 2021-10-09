<template>
    <div class="main_window">
      <div class="sidebar">
        <div class="user_data">
          <p><img :src="'http://127.0.0.1:8000/users/'+user.avatar" alt="Your avatar" class="user_avatar"></p>
          <p class="text">Login: {{user.login}}</p>
          <p class="text">Email: {{user.email}}</p>
        </div>
        <div class="calendars">
          <h2>Calendars</h2>
          <button class="add_calendar" @click="add_calendar">Add new calendar</button>
          <div v-for="calendar in calendars.normal" :id="'container'+calendar.id" :key="calendar.id" class="checkbox">
            <input type="checkbox" :id="'calendar'+calendar.id" :value="calendar.id" :checked="calendar.is_active" @change="(event) =>active_calendars(event.target.checked, calendar.id)">
            <label :for="'calendar'+calendar.id">{{calendar.title}}</label>
            <div class="menu" @click="(event)=>menu_click(event, calendar.id)" :id="'menu'+calendar.id" v-if="!calendar.is_default"></div>
            <span v-else>default</span>
            <ul class="dropdown" :id="'dropdown'+calendar.id" show="false">
              <li @click.prevent="()=>invite(calendar.id)">Invite somebody</li>
              <li @click.prevent="()=>delete_calendar(calendar.id)">Delete</li>
            </ul>
          </div>
          <h2 v-if="calendars.invited">Invite calendars</h2>
          <div v-for="calendar in calendars.invited" :id="'container'+calendar.id" :key="calendar.id" class="checkbox">
            <input type="checkbox" :id="'calendar'+calendar.id" :value="calendar.id" :checked="calendar.is_active" @change="(event) =>active_calendars(event.target.checked, calendar.id)">
            <label :for="'calendar'+calendar.id">{{calendar.title}}</label>
            <div class="menu" @click="(event)=>menu_click(event, calendar.id)" :id="'menu'+calendar.id"></div>
            <ul class="dropdown" :id="'dropdown'+calendar.id" show="false">
              <li @click.prevent="()=>invite(calendar.id)">Invite somebody</li>
              <li @click.prevent="()=>delete_invite(calendar.id)">Delete invite</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="fullcalendar_wrapper">
        <Fullcalendar
            :plugins="calendarPlugins"
            :header="{
                left: 'prev,next today',
                center: 'title',
                right: 'createEvent dayGridMonth,timeGridWeek,timeGridDay'
            }"
            :customButtons="{
              createEvent: {
                text: 'Create event',
                click: newEvent
            }}"
            :buttonText="{
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day',
            }"
            :eventTimeFormat = "{
              hour: '2-digit',
              minute: '2-digit',
              meridiem: true
            }"
            :eventResizableFromStart="true"
            :weekends="true"
            :selectable="true"
            :editable="true"
            :events="events"
            @select="handleSelect"
            @eventClick="handleEventClick"
            @eventResize="updateEvent"
            @eventRender="renderEvent"
            @eventDrop="updateEvent"
            :weekNumbers="true"
            :height="600"
        />
      </div>

        <modals-container />
    </div>
</template>

<script>

import axios from 'axios'

require('@fullcalendar/core/main.min.css')
require('@fullcalendar/daygrid/main.min.css')
require('@fullcalendar/timegrid/main.min.css')

import Fullcalendar from '@fullcalendar/vue'
import DayGridPlugin from '@fullcalendar/daygrid'
import TimeGridPlugin from '@fullcalendar/timegrid'
import InteractionPlugin from '@fullcalendar/interaction'
import Cookies from "js-cookie";

import AddCalendarModal from "./AddCalendarModal";
import EventModal from './EventModal'
import CreateEventModal from "./CreateEventModal";
import InviteModal from "./InviteModal";

export default {
    data: () => ({
        calendarPlugins: [
            DayGridPlugin,
            TimeGridPlugin,
            InteractionPlugin,
        ],
      events: [],
      user: {},
      calendars: [],
      checked: []
    }),
    components: {Fullcalendar},
    mounted() {
      axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
      let calendar_id = this.$route.query.add_calendar
      if(calendar_id) {
        axios.patch('http://127.0.0.1:8000/api/calendars/'+calendar_id+'/invites')
            .catch(err => {
              alert(err)
            })
          .finally(function () {
            window.location.href = '/home'
          })
      }
      axios.all([
        axios.get('http://127.0.0.1:8000/api/events'),
        axios.get('http://127.0.0.1:8000/api/users/my_account'),
        axios.get('http://127.0.0.1:8000/api/calendars')
      ])
      .then(axios.spread((events_res, user_res, calendars_res) => {
        this.events = events_res.data
        this.user = user_res.data
        this.calendars = calendars_res.data
      }))
      .then(()=> {
        document.querySelectorAll('.checkbox').forEach(container=> {
          if(container.children[0].checked) container.setAttribute('atr', 'checked')
        })
      })
      .catch(err => {
        if(err.response.status === 401) {
          window.location.href = '/'
          Cookies.clear()
        }
        else alert(err)
      });
    },
    methods: {
      get_calendars() {
        axios.get('http://127.0.0.1:8000/api/calendars')
            .then(res => {
              this.calendars = res.data
            })
            .catch(err => {
              alert(err)
            })
      },
      menu_click(event, calendar_id) {
        event.preventDefault()
        let dropdown = document.getElementById('dropdown'+calendar_id);

        if(dropdown.getAttribute('show') === 'false') {
          document.querySelectorAll(".dropdown").forEach(el=>el.setAttribute('show', 'false'))
          dropdown.setAttribute('show', 'true')
          window.onclick = function(event) {
            if (!event.target.matches('#menu'+calendar_id)) {
                dropdown.setAttribute('show', 'false')
            }
          }
        }
      },
      active_calendars(checked, calendar_id) {
        let checkbox_container = document.getElementById('container'+calendar_id)

        if(checked) checkbox_container.setAttribute('atr', 'checked')
        else checkbox_container.removeAttribute('atr')

        axios.patch('http://127.0.0.1:8000/api/calendars/'+calendar_id, {is_active: checked})
            .then(() => {
              this.getEvents()
            })
            .catch(err => {
              alert(err)
            })
      },
      add_calendar() {
        this.$modal.show(AddCalendarModal, {get_calendars:this.get_calendars}, {
          width: "400px",
          height: "200px",
          adaptive: true
        })
      },
      invite(calendar_id) {
        this.$modal.show(InviteModal, {calendar_id:calendar_id}, {
          width: "400px",
          height: "200px",
          adaptive: true
        })
      },
      delete_calendar(calendar_id) {
        axios.delete('http://127.0.0.1:8000/api/calendars/'+calendar_id)
            .then(() => {
              this.get_calendars()
            })
            .catch(err => {
              alert(err)
            })
      },
      delete_invite(calendar_id) {
        axios.delete('http://127.0.0.1:8000/api/calendars/'+calendar_id+'/invites')
            .then(() => {
              this.get_calendars()
            })
            .catch(err => {
              alert(err)
            })
      },
      async getEvents() {
        axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
        axios.get('http://127.0.0.1:8000/api/events')
        .then(res => {
          this.events = res.data
        })
        .catch(err => {
          alert(err)
        })
      },
        newEvent(event) {
          event.preventDefault()
            this.$modal.show(CreateEventModal, {event: {category:'reminder'}, getEvents: this.getEvents,format_date: this.format_date}, {
                width: "60%",
                height: "60%",
                adaptive: true
              })
        },
        renderEvent(arg) {
            let span = document.createElement('span')
            arg.el.appendChild(span)
        },
        updateEvent (arg) {
          axios.patch('http://127.0.0.1:8000/api/events/'+arg.event.id, {
            'start': this.format_date(arg.event.start),
            'end': this.format_date(arg.event.end)
          })
          .catch(err => {
            alert(err)
            window.location.reload()
          })
        },
        handleSelect(arg) {
          this.$modal.show(CreateEventModal, {getEvents:this.getEvents,format_date: this.format_date,
            event: {
              start: arg.start,
              end: arg.end,
            } },
    {
            width: "60%",
            height: "60%",
            adaptive: true
          })
        },
        handleEventClick (arg) {
            this.$modal.show(EventModal, {event_id: arg.event.id, getEvents:this.getEvents, format_date: this.format_date}, {
                  width: "70%",
                  height: "70%",
                  adaptive: true
                })
        },
        format_date(date) {
          date = new Date(date)
          let year = this.format_number(date.getFullYear())
          let month = this.format_number(date.getMonth()+1)
          let day = this.format_number(date.getDate())
          let hour = this.format_number(date.getHours())
          let minute = this.format_number(date.getMinutes())
          return `${year}-${month}-${day}T${hour}:${minute}`
        },
        format_number(number) {
          return +number < 10?`0${number}`:number
        },
    }
};

</script>

<style scoped>
.add_calendar {
  display: flex;
  justify-content: center;
  width: 98%;
  margin: 2px 4px 10px 2px;
  background-color: #2b0093;
  font-size: 13pt;
  color: white;
  padding: 2px 10px;
  border: 1px solid grey;
  border-radius: 5px;
  cursor: pointer;
}
.dropdown {
  background-color: #ffffff;
  position: absolute;
  color: black;
  /*border: 1px solid black;*/
  display: none;
  list-style: none;
  margin-top: 20px;
  margin-left: 80px;
  padding-left: 0;
}
.dropdown li {
  padding: 8px;
  border: 1px solid grey;
  cursor: pointer;
}
.dropdown li:hover {
  background-color: #999999;
}
.dropdown[show="true"]{
  display: flex;
  flex-direction: column;
}
.change_profile:hover {
  background-color: black;
  color: white;
}
.menu:after {
  content: '\2807';
  font-size: 15px;
  cursor: pointer;
}
.main_window {
  display: grid;
  grid-template-columns: 300px calc(100% - 300px);
}
.sidebar {
  width: 250px;
  padding: 5px;
}
.user_avatar {
  width: 100px;

}
.user_data {
  margin-bottom: 40px;
}
.calendars {
  text-align: start;
}
.text {
  word-wrap: break-word;
}
input[type=checkbox], input[type=checkbox]+label {
  cursor: pointer;
}
.checkbox {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 2px 10px;
  border: 1px solid grey;
  border-radius: 5px;
  margin: 2px;
  word-break: break-word
}
.checkbox[atr="checked"] {
  background-color: #3c61e7;
  color:white;
}
</style>
