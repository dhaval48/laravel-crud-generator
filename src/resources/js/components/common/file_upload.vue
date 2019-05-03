<template>
<div class="clearfix">
  &nbsp;
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-bordered" v-if="files.length">
            <thead>
                <tr>
                    <th>Uploded File Name</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(file, key) in files">
                    <td>
                      {{ file.name }}
                    </td>
                    <td>
                      <input type='text' class='form-control' v-model="name[key]">
                    </td>
                    <td>
                        <a v-on:click.prevent="removeFile(key)" class="btn btn-flat">
                            <i class="fa fa-trash" style="color:red;"></i>
                        </a>

                        <a :href="'/file/download?file_detail_id='+file.id" class="btn btn-flat" download="download" target="_blank" v-if="file.id">
                          Download
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <button type="button" class="btn btn-link btn-flat" v-on:click="addFiles()">{{this.module.common.add_files}}</button>
      <!-- <button type="button" class="btn btn-info btn-sm" v-on:click="submitFiles()">Upload</button> -->
      <input type="file" class="hide d-none" ref="files" multiple v-on:change="handleFilesUpload()"/>
    </div>
  </div>
</div>
</template>

<script>
  export default {
    props:['module'],
    /*
      Defines the data used by the component
    */
    data(){
      return {
        files: [],
        name: [],

      }
    },

    methods: {

      init:function(){
        if(this.module.id != 0) {
          axios.get(this.module.get_file+'?id='+this.module.id+'&type='+this.module.dir).then(data => {
              if(data.data[0]){
                this.files = data.data[1];
              }
          });
        }
      },

      submitFiles(dir, type_id){
    
        let formData = new FormData();

        for( var i = 0; i < this.files.length; i++ ){
          let file = this.files[i];

          formData.append('upload_files[' + i + ']', file);
          formData.append('file_name['+ i + ']', this.name[i]);
          if(this.module.id != 0) {
            formData.append('file_detail_id['+ i +']', this.files[i].id);
          }
          
        }

        formData.append('type', dir);
        formData.append('type_id', type_id);

        axios.post( '/upload/put',
          formData,
          {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
          }
        ).then(response => {

        })
        .catch(error => {
            this.$parent.form.errorMessage(error.response.data.meta.message);
        });
      },

      handleFilesUpload(){
        // this.files = this.$refs.files.files;
        let uploadedFiles = this.$refs.files.files;

        for( var i = 0; i < uploadedFiles.length; i++ ){
          this.files.push( uploadedFiles[i] );
          if(this.name[i] == undefined) {
            this.name[i] = "";
          }
        }
      },

      addFiles(){
        this.$refs.files.click();
      },

      removeFile( key ){
        if(this.files[key].id != undefined && this.files[key].id != null) {
          axios.get( '/file/delete?file_detail_id='+this.files[key].id).then(response => {

          })
          .catch(error => {
              this.$parent.form.errorMessage(error.response.data.meta.message);
          });
        }
        this.files.splice( key, 1 );
      },
    },
    
    mounted() {
      this.init();
    }
  }
</script>