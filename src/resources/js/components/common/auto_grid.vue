<template>
    <div class="col-md-12">
        <div class="table-scroll">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th v-for="(value,k) in this.elementdata">
                            {{ k }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(n, k) in rows.length">
                        <td>
                            <a v-on:click.prevent="deleteRow(k,grid)" class="btn btn-flat">
                                <i class="fa fa-trash" style="color:red;"></i>
                            </a>
                        </td>

                        <template v-for="(list,index) in grid">
                            <td v-if="list.type == 'input'" style="min-width:100px">
                                <input type='text' :name='list.name' class='form-control' v-model="form[list.name][k]" @input="autoFill(k)">
                            </td>

                            <td v-if="list.type == 'date'" style="min-width:100px">
                                <datepicker v-model="form[list.name][k]" :input-class='"form-control"' :calendar-button-icon='"fa fa-calendar"' :format='"dd/MM/yyyy"' name='list.name'></datepicker>
                            </td>

                            <!-- <td v-if="list.type == 'checkbox'">
                                <datepicker v-model="form[list.name][k]" :input-class='"form-control"' :calendar-button-icon='"fa fa-calendar"' :format='"dd/MM/yyyy"' name='list.name'></datepicker>
                            </td> -->

                            <td v-if="list.type == 'dropdown'" style="min-width:200px">
                                <select class='form-control select-grid' :name='[list.name]' :position='k' v-model='form[list.name][k]'>
                                    <option value=''>Select {{index}}</option>
                                    <option v-for='value in $data[list.name]' :value='value' v-if="list.empty">{{value}}</option>
                                    <option v-for='(value, key) in $data[list.name]' :value='key' v-else>{{value}}</option>
                                </select>
                            </td>
                                      
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>
        <label class="btn btn-primary btn-sm btn-flat" v-on:click.prevent="addRow(grid,rows)">Add New</label>
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
            gridIndex:0,
			type : this.module.type,
			input_type : this.module.input_type,
            value :[],
            key:[],
            table:[],
			// [GridOptionsData]
        }
    },
    methods: {
        
        autoFill(k){
            this.$parent.autoFill(k);
        },

        setIndex(index){
            this.gridIndex = index;
        },
        
        onSearch(search, value) {
            // [GridDropdownFunction]
        },
        
        addRow: function (grid,rows) {

            this.rows.push(++this.index);

            $.each(grid, (i, list) => {
                $.each(rows, (k,value) => {
                    if(k === this.index) {
                        this.form[list.name][this.index] = "";
                    }
                })
            });
            this.$parent.addRow();
        },

        deleteRow: function (k, elementarray) {
            if(this.rows.length != 1) {
                this.rows.splice(k, 1);
                $.each(elementarray, (index, list) => {
                    this.form[list.name].splice(k, 1);
                });
                this.index--;
                this.$parent.deleteRow(k);
            }
        }  
    },
    mounted() {
        if(this.module.id == 0){
            $.each(this.grid, (i, list) => {
                this.form[list.name][this.index] = "";
            });
        }

        $(document).ready( () => {

            // $(document).on('change', '.select-grid', event => {
            //     var input_db_name = $(event.target).attr('name');
            //     var position = $(event.target).attr('position');
            //     this.form[input_db_name][position] = event.target.value;
            // });

        });

        // [GridDropdownSearch]
    }
}
</script>
