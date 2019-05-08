<template>
    <div class="row padding-lr-0">

        <div class="col-sm-6 py-1">
            <div class='input-group'>
                <input type="text" id="q" placeholder="Search" v-on:keyup.enter.prevent="filterList" v-model="q" class="form-control">
                <span class='btn btn-default btn-flat input-group-btn' v-on:click.prevent="filterList"><i class='fa fa-search'></i></span>
            </div>
        </div>

        <div class="col-sm-6 py-1">            

            <div v-if="this.lists.is_visible" class="btn-group"  style="float:right">
                <button  type="button" class="btn btn-primary btn-sm btn-flat dropdown-toggle pull-right" data-toggle="dropdown" tabindex="-1" aria-haspopup="true" aria-expanded="false">
                    {{this.lists.common.export}}
                </button>

                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" :href='this.lists.paginate_route+"?q="+q+"&pdf=1"'>{{this.lists.common.pdf}}</a>
                    <a class="dropdown-item" target="_blank" :href='this.lists.paginate_route+"?q="+q+"&csv=1"'>{{this.lists.common.csv}}</a>
                </div>
            </div>

            <a v-if="this.lists.permissions['store_'+this.lists.dir]" :href="this.lists.create_route" class="btn btn-primary btn-flat btn-sm pull-right" style="float:right; margin-right:5px">{{this.lists.lang.create_title}}</a>
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