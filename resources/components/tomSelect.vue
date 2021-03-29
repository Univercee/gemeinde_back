<template>
    <div class="input-group">
        <input class="form-control" ref="tomSelect" placeholder="PLZ oder Ortsname" @change="returnSelected()" />
        <button class="btn btn-primary" type="submit"><svg class="" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M19 17l-5.15-5.15a7 7 0 1 0-2 2L17 19zM3.5 8A4.5 4.5 0 1 1 8 12.5 4.5 4.5 0 0 1 3.5 8z"/></svg></button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            ts: null
        }
    },
    props: {
        locations: Array
    },
    methods: {
        initTomSelect() {
            this.ts = new TomSelect(this.$refs.tomSelect, {
                openOnFocus: false,
                maxItems: 1,
                maxOptions: 20,
                valueField: 'id',
                searchField: ['display_name'],
                labelField: 'display_name',
                sortField: 'display_name',
                closeAfterSelect: true,
                searchConjunction: 'and',
                options: this.locations,
                render: {
                    'no_results': function(data,escape) {
			            return '<div class="no-results">Unknown location "'+escape(data.input)+'".</div>';
		            }
                }
            });
            this.ts.on('focus', () => {
                this.ts.clear();
            });
        },

        showDropdownList() {
            this.ts.open();
        },

        returnSelected() {
            this.$emit('tsChanged', this.ts.getValue())
        }
    },
    mounted() {
        this.initTomSelect()
    },
}
</script>
<style>
  .ts-input {
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