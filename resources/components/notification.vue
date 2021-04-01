<template>
    <div>
        <div ref="alert" class="alert alert-dismissible fade" :class="{'alert-danger':error, 'alert-success':!error, 'show':show, 'd-none':!show}">
            <strong>{{message}}</strong>
            <button v-on:click="show=false" class="btn-close"></button>
        </div>
    </div>
</template>
<script>
export default {
    props:{
        message:{
            type: String,
            default:null
        },
        error:{
            type: Boolean,
            default:null
        }
    },
    watch:{
        message: async function(newVal, oldVal) {
            this.show = true
        }
    },
        data() {
        return {
            show:false
        }
    },
}
</script>
