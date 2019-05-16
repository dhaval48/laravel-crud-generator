<template>
    <div>
        <list_header :lists='this.module'></list_header>
        
        <div class="clearfix">&nbsp;</div>
        <div class="table-scroll">
            <table class="table" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th v-for="(value,key) in lists.list_data">{{ key }}</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>
                <tbody v-if="lists.lists.data.length > 0">
                    <tr v-for="list in lists.lists.data">
                        
                        <td v-for="(value,key) in lists.list_data">
                            <template v-if="value =='date'">
                                {{ list[value] | dmy }}
                            </template>
                            <template v-else-if = "value.indexOf('.') > 0">
                                {{ list | relation(value) }}
                            </template>
                            <template v-else>
                                {{list[value] != null ? list[value] : '-'}}                                           
                            </template>
                        </td>
                        <td>

                            <div class="btn-group">
                                <button  type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle pull-right" data-toggle="dropdown" tabindex="-1" aria-haspopup="true" aria-expanded="false">
                                    Action
                                </button>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a v-if="lists.permissions['update_'+lists.dir]" class="dropdown-item" :href='module.edit_route+"/"+list.id'><i class="fa fa-edit" style="color: green;" data-toggle="tooltip" data-placement="top" data-original-title="Edit"></i>{{lists.common.edit}}</a>
                                    <a v-if="lists.permissions['delete_'+lists.dir]" class="dropdown-item" href="javascript:void(0);" @click="deleteRecord(list.id)"><i class="fa fa-trash" style="color: red;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>{{lists.common.delete}}</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
                <tbody v-else>
                    <tr>
                        <td class="text-center" colspan="20">
                            {{lists.common.no_data}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <paginate-links :lists='this.lists'></paginate-links>
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
