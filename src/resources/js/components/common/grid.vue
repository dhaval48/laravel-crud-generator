<template>
    <div class="col-md-12">
        <div class="table-scroll">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="10">#</th>
                        <th v-for="(value,key) in this.elementdata">
                            {{ key }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(n, key) in rows.length">
                        <td>
                            <a v-on:click.prevent="deleteRow(key,grid)" class="btn btn-flat">
                                <i class="fa fa-trash" style="color:red;"></i>
                            </a>
                        </td>

                        <template v-for="(list,index) in grid">
                            <td v-if="list.type == 'input'" style="min-width:100px">

                                <input type='text' :name='list.name' class='form-control' v-model="form[list.name][key]">
                            </td>

                            <td v-if="list.type == 'date'" style="min-width:100px">
                                <datepicker v-model="form[list.name][key]" :input-class='"form-control"' :calendar-button-icon='"fa fa-calendar"' :format='"dd/MM/yyyy"' name='list.name'></datepicker>
                            </td>

                            <td v-if="list.type == 'dropdown'" style="min-width:200px">

                                <select class='form-control select-grid' :name='[list.name]' :position='key' v-model='form[list.name][key]'>
                                    <option value=''>Select {{index}}</option>
                                    <option v-for='value in $data[list.name]' :value='value' v-if="list.empty">{{value}}</option>
                                    <option v-for='(value, k) in $data[list.name]' :value='k' v-else>{{value}}</option>
                                </select>

                            </td>
                                      
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>
        <label v-if="this.module.dir != 'languagetranslet'" class="btn btn-primary btn-sm btn-flat" v-on:click.prevent="addRow(grid,rows)">{{this.module.common.add_new}}</label>
    </div>
</template>

<script>
import moment from 'moment';
export default {
	
	props:['module','elementdata','elementrow','rowcount'],
	data(){
        return {
            rows:this.elementrow,
            grid:this.elementdata,
            index:this.rowcount,
            form:this.module.fillable,
            type : this.module.type,
            gridIndex:0,
			
			// [GridOptionsData]
        }
    },
    methods: {
        
        addRow: function (grid,rows) {

            this.rows.push(++this.index);

            $.each(grid, (i, list) => {
                $.each(rows, (key,value) => {
                    if(key === this.index) {
                        this.form[list.name][this.index] = "";
                    }
                })
            });
        },

        deleteRow: function (key, elementarray) {
            if(this.rows.length != 1){
                this.rows.splice(key, 1);
                $.each(elementarray, (index, list) => {
                    this.form[list.name].splice(key, 1);
                });
                if(this.module.id != 0) {
                    if(this.form.module_input_id[key]) {
                        this.form.module_input_id.splice(key,1);
                    }
                }
                this.index--;
            }
        }  
    },
    mounted() {

        if(this.module.id == 0){
            $.each(this.grid, (i, list) => {
                this.form[list.name][this.index] = "";
            });
        }
        // [GridDropdownSearch]
    }
}
</script>
