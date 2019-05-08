<template>
<div>
    <div class="col-md-12">
	    <h5>Activity</h5>
	    <hr>
	    <ul class="timeline" @changed = "init">
		    <li v-for="(activity,index) in activities">
	            <!-- <i class="fa fa-user bg-aqua"></i> -->

              	<div class="timeline-item">
                	<span v-html="activity.message"></span><br/>
        					<span class="opacity-50">
        						{{activity.created_at | ago}}
        					</span>
              	</div>
            </li>
        </ul>
    </div>
</div>
</template>

<script>

export default {
	
	props:['module'],
	data(){
        return {
            activities:[],
        }
    },
    methods: {
       	init:function(){
       		if(this.module.id != 0) {
	            axios.get(this.module.get_activity+'?id='+this.module.id+'&type='+this.module.dir).then(data => {
	                this.activities = data.data;
	            });
	        }
       	}
    },
    mounted() {
        this.init();
    }
}
</script>