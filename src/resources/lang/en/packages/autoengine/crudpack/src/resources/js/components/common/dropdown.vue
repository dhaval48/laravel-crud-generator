<template>
<div> 
    <v-select 
        :options="data"
        placeholder="Select location"
        v-model='this.model'
    ></v-select>
</div>
</template>

<script>
export default {
    
    props: ['url','options','model'],
    data(){
        return {
            data:[],
        }
    },

    mounted() {
        if(this.url) {
            axios.get(this.url).then(data => {
                this.data = Object.values(data.data);
            });
        } else {
            this.data = this.options;
        }
    }
}
</script>
