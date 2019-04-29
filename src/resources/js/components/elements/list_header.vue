<template>
    <div> 
        <div class="action_btn col-md-8">
            <a :href="this.lists.create_route" class="btn btn-primary btn-flat btn-sm">{{this.lists.lang.create_title}}</a>
           
            <button v-if="this.lists.is_visible" type="button" class="btn btn-warning btn-sm btn-flat dropdown-toggle" data-toggle="dropdown" tabindex="-1" aria-expanded="false">
                {{this.lists.common.export}}
            </button>

            <ul class="dropdown-menu" role="menu">
                <li><a target="_blank" :href='this.lists.paginate_route+"?q="+q+"&pdf=1"'>{{this.lists.common.pdf}}</a></li>
                <li><a target="_blank" :href='this.lists.paginate_route+"?q="+q+"&csv=1"'>{{this.lists.common.csv}}</a></li>
            </ul>
        </div>
        <div class="col-md-4 action_btn" >
            <div class='input-group'>
                <input type="text" id="q" placeholder="Search" v-on:keyup.enter.prevent="filterList" v-model="q" class="form-control">
                <span class='btn btn-default btn-flat input-group-btn' style="font-size:unset !important" v-on:click.prevent="filterList"><i class='fa fa-search'></i></span>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props:['lists'],   

    data(){
        return {
            q:'',
        }
    },

    watch:{
        
    },

    methods: {
        filterList:function(){
            axios.get(this.lists.paginate_route+'?q='+this.q).then(data => data.data.data)
            .then(data => {
                this.$parent.q = this.q;
                this.$parent.lists = data;
            });
        },
    },

    mounted(){
        
    }
}
</script>