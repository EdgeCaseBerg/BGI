jQuery( document ).ready(function( $ ) {
	var timelineURI = window.bgidomain + "timeline.cgi"

	function n2c(name){
		var colors = ['red','blue','yellow','green','purple']
		var color = 1;
		for(idx in name){
			color += name.charCodeAt(idx)*idx
		}
		return colors[color % colors.length];
	}
	
	var datasets = []
	var labels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

	/* Retrieve and create the timeline for each month */
	$.get(timelineURI, function(timeline){
		for (var i = timeline.length - 1; i >= 0; i--) {
			var accountName = timeline[i].name
			
			/* Initialize data counts*/
			var data = []
			for (var k= labels.length - 1; k >= 0; k--) {
				data.push(0)
			};
			var label = accountName
			var color = n2c(accountName)

			for (var j = 0; j < timeline[i].items.length; j++) {
				data[new Date(parseInt(timeline[i].items[j].date)*1000).getMonth()] += ( timeline[i].items[j].amount )
			};

			datasets.push({
				label: accountName,
				fillColor: color,
				data: data
			})
		
		}
		var data = {
    		labels: labels,
	    	datasets: datasets
    	}
    	var ctx = document.getElementById("timelinecanvas").getContext("2d");
    	var myLineChart = new Chart(ctx).Line(data, {});
	})
})