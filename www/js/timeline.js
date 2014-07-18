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
	
	var monthDataSets = []
	var monthLabels = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]

	var dayDataSets = []
	var dayLabels = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]

	/* Retrieve and create the timeline for each month */
	$.get(timelineURI, function(timeline){
		for (var i = timeline.length - 1; i >= 0; i--) {
			var accountName = timeline[i].name
			
			/* Initialize data counts*/
			var monthData = []
			var dayData = []
			for (var k= monthLabels.length - 1; k >= 0; k--) {
				monthData.push(0)
			};
			for (var k = dayLabels.length - 1; k >= 0; k--) {
				dayData.push(0)
			};
			var label = accountName
			var color = n2c(accountName)

			for (var j = 0; j < timeline[i].items.length; j++) {
				monthData[new Date(parseInt(timeline[i].items[j].date)*1000).getMonth()] += ( timeline[i].items[j].amount )
				dayData[new Date(parseInt(timeline[i].items[j].date)*1000).getDay()] += ( timeline[i].items[j].amount )
			};

			monthDataSets.push({
				label: accountName,
				fillColor: color,
				data: monthData
			})
			
			dayDataSets.push({
				label: accountName,
				fillColor: color,
				data: dayData
			})
		}
		var monthData = {
    		labels: monthLabels,
	    	datasets: monthDataSets
    	}
    	var mctx = document.getElementById("monthcanvas").getContext("2d");
    	var myLineChart = new Chart(mctx).Bar(monthData, {});

    	var dayData = {
    		labels: dayLabels,
    		datasets: dayDataSets
    	}
		var dctx = document.getElementById("daycanvas").getContext("2d");
    	var myLineChart = new Chart(dctx).Bar(dayData, {});

	})
})