<template>
    <div class="input-group">
        <input class="form-control" ref="tomSelect" placeholder="PLZ oder Ortsname" v-on:change="emitData()">
    </div>
</template>
<script>
export default {
    data() {
        return {
            ts:null,
            created:false
        }
    },
    props:{
        data:null,
        id:null,
        index:null
    },
    methods: {
        initTomSelect() {
            this.ts = new TomSelect(this.$refs.tomSelect, {
                dropdownClass: 'form-control',
                openOnFocus: false,
                maxItems: 1,
                valueField: 'id',
                searchField: ['name','zipcode'],
                labelField: 'name',
                sortField: 'name',
                closeAfterSelect: true,
                searchConjunction: 'or',
                options: this.$props.data,
                render: {
                    'no_results':function(data,escape){
			            return '<div class="no-results">😞 At the moment we are not offering any services in "'+escape(data.input)+'". Please <a href="https://docs.google.com/forms/d/e/1FAIpQLScqylrgpCicOf3k3NNkKDdF7Q3MX7XBdfsFmvZbzuWItZOt1A/viewform?vc=0&c=0&w=1&flr=0&gxids=7628&entry.839337160='+escape(data.input)+'">let us know</a> you are interested to join and we will notify you once we add your location.</div>';
		            }
                }
            });
            if(this.$props.id != null){
                this.ts.setValue(this.$props.id)
            }
            this.created = true
        },
        emitData(){
            if(this.created){
                this.$emit('emitData', this.ts.getValue(), this.$props.index)
            }
        }
    },
    mounted() {
        this.initTomSelect()
    },
}
</script>
<style>
  .ts-input {
      padding: 0;
      margin: 0;
    border: none;
    padding: none;
    display: inline-block;
    width: 100%;
    overflow: hidden;
    position: relative;
    z-index: 1;
    box-sizing: content-box;
    box-shadow: none;
    border-radius: none;
  }


  .ts-input.focus {
    box-shadow: none;
  }

  .ts-control.single .ts-input:after {
    content: none;
  }
</style>