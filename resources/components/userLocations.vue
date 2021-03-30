<template>
    <div>
        <div class="accordion" id="accordionLocations">
            <div class="accordion-item" :key="location" v-for="(location, index) in user_locations">
                <h2 class="accordion-header" :id="'heading'+location.id">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#location'+location.id" aria-expanded="false" :aria-controls="'location'+location.id">
                    <h3>{{location.title}} {{getPrimaryLocationById(location.location_id)}} {{location.street_name}} {{location.street_number}}</h3>
                </button>
                </h2>
                <div :id="'location'+location.id" class="accordion-collapse collapse" :aria-labelledby="'heading'+location.id" data-bs-parent="#accordionLocations">
                    <div class="accordion-body">
                        <form @submit.prevent="updateLocation(index)">
                            <div class="form-group">
                                <label for="">Title </label>
                                <input type="text" v-model="location.title">
                            </div>
                            <div class="form-group">
                                <label for="">Location </label>
                                <tomSelect v-if="primary_locations" :locations="primary_locations" :selectedValue="parseInt(location.location_id)" @tsChanged="location.location_id = $event"></tomSelect>
                            </div>
                            <div class="form-group">
                                <label for="">Street name </label>
                                <input type="text" v-model="location.street_name">
                            </div>
                            <div class="form-group">
                                <label for="">Street number </label>
                                <input type="text" v-model="location.street_number">
                            </div>
                            <div class="form-group" v-if="index!=new_index">
                                <input type="submit">
                            </div>
                        </form>
                        <div class="btn btn-danger" v-if="index!=new_index" v-on:click="deleteLocation(index)">Delete</div>
                        <div class="btn btn-primary" v-if="index==new_index" v-on:click="addLocation()">Add</div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</template>
<script>
import tomSelect from './tomSelect.vue'
export default {
    components: {
        tomSelect
    },
    data(){
        return{
            user_locations:null,
            primary_locations:null,
            new_index:null
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
                                        title:'Add new location', 
                                        street_name:null, 
                                        street_number:null})
                this.new_index = this.user_locations.length-1
            }).catch(()=>{
              //catching errors
            })
        },
        async updateLocation(index){
            await axios({
                method: 'patch',
                url: '/profile/userLocations',
                data:this.user_locations[index]
            }).then(()=>{
                this.fetchLocations()
            }).catch(()=>{
              //catching errors
            })
        },
        async addLocation(){
            await axios({
                method: 'post',
                url: '/profile/userLocations',
                data: this.user_locations.pop()
            }).then(()=>{
                this.fetchLocations()
            }).catch(()=>{
            })
        },
        async deleteLocation(index){
            await axios({
                method: 'delete',
                url: '/profile/userLocations',
                data:{
                    id:this.user_locations[index].id
                }
            }).then(()=>{
                this.fetchLocations()
            }).catch(()=>{
              //catching errors
            })
        },
        async getPrimaryLocations(){
            await axios({
                method: 'get',
                url: '/locations/all',
            }).then((response)=>{
                this.primary_locations = response.data
            }).catch(()=>{
              //catching errors
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
        getLocationId(id){
        }
    },
    async mounted() {
        await this.getPrimaryLocations()
        await this.fetchLocations()
    }
}
</script>