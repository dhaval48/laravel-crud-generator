
import Vue from "vue";
import moment from 'moment';

export default {
    install(Vue) {
		Vue.filter('ymdt',function(value){
			return moment(value).format("D MMM YYYY HH:mm a");
		});

		Vue.filter('dmy',function(value){
			if(value != null) {
				return moment(value,'YYYY-MM-DD').format("DD/MM/YYYY");
			} 
			return '-';
		});

		Vue.filter('ago',function(value){
			return moment(value).fromNow();
		});

		Vue.filter('currency',function(value) {
	    	return "₹ " + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		});
		Vue.filter('round',function(value) {
	    	return "₹ " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		});

		Vue.filter('relation',function(list, name) {
	    	var arr = name.split('.');
	    	if(arr.length == 2) {
		    	return list[arr[0]][arr[1]];
		    }
		    if(arr.length == 3) {
		    	return list[arr[0]][arr[1]][arr[2]];
		    }
		    if(arr.length == 4) {
		    	return list[arr[0]][arr[1]][arr[2]][arr[3]];
		    }
		});
		
	}
}


