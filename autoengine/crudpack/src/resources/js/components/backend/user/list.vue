<template>
    <div>
        <list_header :lists='this.module'></list_header>
        
        <div class="clearfix"></div>
        
        <div class="box-body">
            <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th v-for="(value,key) in lists.list_data">{{ key }}</th>
                        <th>{{this.module.common.date}}</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
                <tbody v-if="lists.lists.data.length > 0">
                    <tr v-for="list in lists.lists.data">
                        
                        <td v-for="(value,key) in lists.list_data">
                            {{list[value] ? list[value] : '-'}}
                        </td>
                        <td>
                            {{ list.created_at | dmy }}
                        </td>
                        

                        <td class="action_button" width="100px">
                            <div class="dropdown">
                                <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu drop">
                                    <li>
                                        <a v-if="lists.permissions['update_'+lists.dir]" :href='module.edit_route+"/"+list.id'><i class="fa fa-edit" style="color: green;" data-toggle="tooltip" data-placement="top" data-original-title="Edit"></i>{{lists.common.edit}}</a>
                                    </li>
                                    <li>
                                        <a v-if="lists.permissions['delete_'+lists.dir]" href="javascript:void(0);" @click="deleteRecord(list.id)"><i class="fa fa-trash" style="color: red;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>{{lists.common.delete}}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
                <tbody v-else>
                    <tr>
                        <td class="text-center" colspan="6">
                            No data found.
                        </td>
                    </tr>
                </tbody>
            </table>
            <paginate-links :lists='this.lists'></paginate-links>
        </div>
        
    </div>
</template>


<script>
import list_header from '../.././elements/list_header';

export default {
    props:['module'],
    components : {list_header},
    data(){
        return {
            q:'',
            lists:this.module,
            form: new Form(this.module.fillable)

        }
    },

    methods: {

        deleteRecord(id) {
            this.form.id = id
            this.$swal({
                title: 'Are you sure?',
                text: 'Are you sure you want to delete this '+this.module.dir+' ?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes Delete it!',
                cancelButtonText: 'No, Keep it!',
                showCloseButton: true,
                showLoaderOnConfirm: true
            }).then((result) => {
                if(result.value) {
                    this.form.post(this.module.destory_route).then(response =>{
                        this.lists = response.data;
                    });
                }
            });
        }
    },

    mounted(){
    }
}
</script>
