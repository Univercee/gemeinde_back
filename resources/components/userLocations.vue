<template>
    <div>
        <h3>My locations</h3>
        <div class="accordion mb-4" id="accordionLocations">
            <div class="accordion-item" :class="{'mt-1':index==new_index}" :key="location" v-for="(location, index) in user_locations">
                <h4 class="accordion-header" :id="'heading'+location.id">
                    <button v-if="index!=new_index" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#location'+location.id" aria-expanded="false" :aria-controls="'location'+location.id">
                        <h4>{{location.title==''?'Untitled':location.title}}</h4>
                    </button>
                    <button v-if="index==new_index" class="btn btn-block btn-outline-primary" type="button" data-bs-toggle="collapse" :data-bs-target="'#location'+location.id" aria-expanded="false" :aria-controls="'location'+location.id">
                        <h3>+</h3>
                    </button>
                </h4>
                <div :id="'location'+location.id" class="accordion-collapse collapse" :aria-labelledby="'heading'+location.id" data-bs-parent="#accordionLocations">
                    <div class="accordion-body">
                        <form @submit.prevent="updateLocation(index);">
                            <div class="form-group row mb-4">
                                <div class="col-1">
                                    <img class="form-img" src="resources/assets/images/profile/home.png" alt="Title">
                                </div>
                                <div class="col-11">
                                    <input style="width:100%" type="text" v-model="location.title" placeholder="Location title">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <div class="col-1">
                                    <img class="form-img" src="resources/assets/images/profile/zip.png" alt="Location">      
                                </div>
                                <div class="col-11">
                                    <tomSelect :viewSearchButton="false" v-if="primary_locations" :locations="primary_locations" :selectedValue="parseInt(location.location_id)" @tsChanged="location.location_id = $event"></tomSelect>
                                </div>
                            </div>
                            
                            <div class="form-group form-group row mb-4">
                                <div class="col-1">
                                    <img class="form-img" src="resources/assets/images/profile/location.png" alt="Address">
                                </div>
                                <div class="col-11 d-flex justify-content-between">
                                    <input style="width:70%" type="text" placeholder="Street name" v-model="location.street_name">
                                    <input style="width:26%" type="text" placeholder="Street number" v-model="location.street_number">
                                </div>
                            </div>
                            <locationServices :userLocation="location.id" :location="location.location_id" :channels="channels" :ref="'services'+index"></locationServices>
                            <div class="d-flex flex-column justify-content-end" v-if="index!=new_index">
                                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                                <button class="btn btn-outline-danger btn-sm mt-3" v-on:click="deleteLocation(index)">Delete</button>
                            </div>
                            <div class="d-flex justify-content-end" v-if="index==new_index">
                                <button class="btn btn-primary btn-sm" v-on:click="addLocation()">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
        
    </div>
</template>
<script>
import tomSelect from './tomSelect.vue'
import locationServices from './locationService.vue'
export default {
    components: {
        tomSelect,
        locationServices
    },
    data(){
        return{
            user_locations:null,
            primary_locations:null,
            new_index:null,
            channels:null
        }
    },
    methods: {
        async fetchLocations(){
            await axios({
                method: 'get',
                url: '/profile/userLocations'
            }).then((response)=>{
                this.user_locations = response.data
                this.user_locations.push({location_id:null, 
                                        title:null, 
                                        street_name:null, 
                                        street_number:null})
                this.new_index = this.user_locations.length-1
            }).catch(()=>{
               this.user_locations = []
                this.user_locations.push({location_id:null, 
                                        title:null, 
                                        street_name:null, 
                                        street_number:null})
                this.new_index = this.user_locations.length-1
            })
        },
        async updateLocation(index){
            this.$refs['services'+index].setServices(this.user_locations[index].location_id)
            await axios({
                method: 'patch',
                url: '/profile/userLocations',
                data:this.user_locations[index]
            }).then(response=>{
                this.notify(response.data.message, false)
                this.fetchLocations()
            }).catch(err=>{
                this.notify(err.response.data.message, true)
            })
        },
        async addLocation(){
            if(this.user_locations[this.new_index].location_id){
                await axios({
                    method: 'post',
                    url: '/profile/userLocations',
                    data: this.user_locations.pop()
                }).then(response=>{
                    this.notify(response.data.message, false)
                    this.fetchLocations()
                }).catch(err=>{
                    this.notify(err.response.data.message, true)
                    this.user_locations.push({location_id:null, 
                                            title:null, 
                                            street_name:null, 
                                            street_number:null})
                    this.new_index = this.user_locations.length-1
                })
            }
        },
        async deleteLocation(index){
            this.$refs['services'+index].deleteServices(this.user_locations[index].location_id)
            await axios({
                method: 'delete',
                url: '/profile/userLocations',
                data:{
                    id:this.user_locations[index].id
                }
            }).then(response=>{
                this.notify(response.data.message, false)
                this.fetchLocations()
            }).catch(err=>{
              this.notify(err.response.data.message, true)
            })
        },
        async getPrimaryLocations(){
            await axios({
                method: 'get',
                url: '/locations',
            }).then((response)=>{
                this.primary_locations = response.data
            }).catch(()=>{
            })
        },
        getPrimaryLocationById(id){
            for(let i=0; i<this.primary_locations.length; i++){
                if(this.primary_locations[i].id == id){
                    return this.primary_locations[i].name
                }
            }
            return null
        },
        async getChannels(){
            await axios.get("/profile/channels").then((response) => (
                this.channels = response.data
            ))
        },
        notify(message, isError){
            this.$emit('notify', message, isError)
        }
    },
    async mounted() {
        await this.getPrimaryLocations()
        await this.fetchLocations()
        await this.getChannels()
    }
}
</script>
<style lang="">
    button:active:focus, button:active, button:focus, input:active:focus, input:active, input:focus {
        box-shadow: none !important;
    }   
    .add-btn{
        height: 100%;
        width: 100%;
    }
    .form-img{
        height: 25px;
        width: 25px; 
    }
    .arrow{
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    .ts-input{
        width: 100%;
        padding: 0;
    }
    .item, .ts-input input{
        width: 100%;
        height: 28px;
        padding: 1px 2px !important;
        font-size: 15px;
        line-height: 26px;
        border-color: rgb(118, 118, 118);
    }
    .ts-input.focus {
        box-shadow: none;
    }
    .ts-control.single .ts-input:after {
        content: none;
        margin: 0;
    }
    .ts-control{
        height: 30px;
        padding: 0;
        margin: 0;
        border-color: rgb(118, 118, 118);
    }
</style>