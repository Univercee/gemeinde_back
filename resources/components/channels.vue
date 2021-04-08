<template>
  <div id="channels-container">
    <h3>{{$root.t('channels_1')}}</h3>
    <div class="accordion accordion-flush" id="accordionFlushExample">
      <div class="accordion-item">
        <h2 class="accordion-header" v-bind:id="'flush-heading-email'">
          <button class="accordion-button collapsed d-flex justify-content-center" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#flush-collapse-email'" aria-expanded="false" v-bind:aria-controls="'#flush-collapse-email'">
            <div class="col-3"><h4>Email:</h4></div>
            <div class="col-8"><h5>{{email??$root.t('channels_2')}}</h5></div>
          </button>
        </h2>
        <div v-bind:id="'flush-collapse-email'" class="accordion-collapse collapse" v-bind:aria-labelledby="'flush-heading-email'" v-bind:data-bs-parent="'#accordionFlushExample'">
          <div class="accordion-body">
            <div v-if="email">
              <button v-on:click="deleteEmailChannel()" role="button" class="btn btn-outline-danger btn-sm" :class="{'disabled':!tg}">{{tg?'Disconnect':$root.t('channels_3')}}</button>
            </div>
            <div v-else>
              <emailChannel :isChannel="true"></emailChannel>
            </div>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" v-bind:id="'flush-heading-tg'">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" v-bind:data-bs-target="'#flush-collapse-tg'" aria-expanded="false" v-bind:aria-controls="'#flush-collapse-tg'">
            <div class="col-3"><h4>Telegram:</h4></div>
            <div class="col-8"><h5>{{tg??$root.t('channels_2')}}</h5></div>
          </button>
        </h2>
        <div v-bind:id="'flush-collapse-tg'" class="accordion-collapse collapse" v-bind:aria-labelledby="'flush-heading-tg'" v-bind:data-bs-parent="'#accordionFlushExample'">
          <div class="accordion-body">
            <div v-if="tg">
              <button v-on:click="deleteTgChannel()" role="button" class="btn btn-outline-danger btn-sm" :class="{'disabled':!email}">{{email?'Disconnect':$root.t('channels_3')}}</button>
            </div>
            <div v-else class="d-flex justify-content-center">
              <tgChannel :isChannel="true" :dataSize="'large'" v-on:verifyTgChannel="getChannels()"></tgChannel>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<i18n>
{
  "en":{
    "channels_1":"My Notification Channels",
    "channels_2":"Not connected",
    "channels_3":"You can't disconnect the only one channel"
  },
  "de":{
    "channels_1":"My Notification Channels",
    "channels_2":"Not connected",
    "channels_3":"You can't disconnect the only one channel"
  }
}
</i18n>

<script>
import tgChannel from './tgLogin.vue'
import emailChannel from './emailLogin.vue'
export default {
  components: {
    tgChannel,
    emailChannel
  },
  data() {
    return {
      tg:null,
      email:null
    }
  },
  methods: {
    async getChannels(){
      await axios.get("/profile/channels").then(response => (
        this.tg = response.data.tg,
        this.email = response.data.email
      ))
    },

    async deleteEmailChannel(){
      axios({
        method: 'delete',
        url: '/profile/channels/email/delete'
      }).then(response=>{
        this.getChannels()
        this.notify(response.data.message, false)
      }).catch(err=>{
        this.notify(err.response.data.message, true)
      })
    },

    async deleteTgChannel(){
      axios({
        method: 'delete',
        url: '/profile/channels/tg/delete'
      }).then(response=>{
        this.getChannels()
        this.notify(response.data.message, false)
      }).catch(err=>{
        this.notify(err.response.data.message, true)
      })
    },
    notify(message, isError){     
      this.$emit('notify', message, isError)
    }
  },
  async mounted(){
    await this.getChannels()
  }
}
</script>
<style>
  h4{
    padding: 0;
    margin: 0;
  }
</style>