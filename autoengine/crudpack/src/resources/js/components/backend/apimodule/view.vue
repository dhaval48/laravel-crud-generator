<template>
    <div>
        <template v-if="module.lists">
            <list :module='this.module'></list>
        </template>
        <template v-else>
            <form_html 
                :formObj='form'
                :module='this.module' 
            ></form_html>

        </template>
    </div><!--end .row -->
</template>

<script>
import form_html from './html.vue';
import list from './list.vue';

export default {
    props:['module'],   
    components : {form_html,list},

    data(){
        return {
            message_type:'',
            form: new Form(this.module.fillable)
        }
    },

    methods: {
    
    },

    mounted(){
        
    }
}
</script>
